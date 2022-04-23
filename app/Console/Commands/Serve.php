<?php

namespace App\Console\Commands;

use App\Messages\AuthError;
use App\Messages\AuthSuccess;
use App\Messages\ChannelList;
use App\Messages\ChannelMessage;
use App\Messages\ErrorMessage;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Console\Command;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use Throwable;

class Serve extends Command
{
    protected $signature = 'serve';
    protected $description = 'Command description';

    protected array $channels = [];
    protected array $users = [];

    protected function load_channels(): void
    {
        foreach (Channel::query()->cursor() as $v) {
            $this->channels[$v->id] = (object)$v->toArray();
        }
    }

    protected function login(int $id, ?string $token)
    {
        $user = User::where('api_token', $token)->first();

        if ($user) {
            $this->users[$id] = $user;

            return true;
        }

        return false;
    }

    public function handle(): int
    {
        $this->load_channels();

        $server = new Server('0.0.0.0', 9101);

        $server->on('Start', function (Server $server) {
            $this->info('Server was started');
        });

        $server->on('Open', function (Server $server, Request $request) {
            $this->info("Client {$request->fd} connected");
            //$server->tick(1000, function() use ($server, $request) { $server->push($request->fd, json_encode(["hello", time()])); });
        });

        $server->on('Message', function (Server $server, Frame $frame) {
            $token = json_decode($frame->data)?->token ?? null;

//            $this->info("Received message: {$frame->data}");

            if (!($this->users[$frame->fd] ?? null)) { // If user not logged in
                if ($this->login($frame->fd, $token)) {
                    $server->push($frame->fd, new AuthSuccess());

                    $this->info('User logged in');
                } else { // User auth error
                    $server->push($frame->fd, new AuthError());
                    $server->close($frame->fd);

                    $this->info('User auth error, closing connection');
                }
            } else { // If user logged in, process message
                $this->process_message($server, $frame);
//                $server->push($frame->fd, $this->process_message($frame->data));
            }
        });

        $server->on('Close', function (Server $server, int $fd) {
            unset($this->users[$fd]);

            $this->info("Client {$fd} connection closed");
        });

        $server->on('Disconnect', function (Server $server, int $fd) {
            $this->info("Client {$fd} connection disconnected");
        });

        $server->start();

        return 0;
    }

    protected function process_message(Server $server, Frame $frame): string
    {
        $request = json_decode($frame->data);
        $response = null;

        switch (mb_strtoupper($request->type ?? null)) {
            case 'MESSAGE':
                switch (mb_strtoupper($request->target ?? null)) {
                    case 'CHANNEL':
                        $channelId = $request->channel_id ?? null;

                        if ($channelId && array_key_exists($channelId, $this->channels)) {
                            $message = $request->message ?? null;


                            foreach ($this->users as $fd => $user) {
                                if ($user->channels->contains('id', $channelId)) {
                                    try {
                                        $server->push($fd, new ChannelMessage($user->id, $channelId, $message));
                                    } catch (Throwable $e) {
                                        $this->error($e->getMessage());
                                    }
                                }
                            }

                            $this->info("Message: {$message}");
                        } else {
                            $response = new ErrorMessage(406, 'Channel not found');
                        }
                        break;

                    case 'COMMENT':

                        break;
                }

                // @TODO проверка на право/возможность писать в канал
                if (array_key_exists($request->channel_id ?? null, $this->channels)) {
                    $response = new ChannelMessage(1, $request->channel_id, $request->message);
                } else {
                    $server->push($frame->fd, new ErrorMessage(406, 'Channel not found'));
                }
                break;

            case 'REQUEST':
                switch (mb_strtoupper($request->target ?? null)) {
                    case 'CHANNEL_LIST':
                        $server->push($frame->fd, new ChannelList($this->channels));
                        break;
                }
                break;

            default:
                dump($request);
                $this->error("Incorrect message type");
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
