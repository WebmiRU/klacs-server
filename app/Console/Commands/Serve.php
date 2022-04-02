<?php

namespace App\Console\Commands;

use App\Messages\ChannelList;
use App\Messages\ChannelMessage;
use App\Messages\ErrorMessage;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Console\Command;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class Serve extends Command
{
    protected $signature = 'serve';
    protected $description = 'Command description';

    protected array $channels = [];

    protected array $tokens = [];
    protected array $users = [];

//    public function __construct()
//    {
//        parent::__construct();
//    }

    protected function load_channels(): void
    {
        foreach (Channel::query()->cursor() as $v) {
            $this->channels[$v->id] = (object)$v->toArray();
        }
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
            $token = json_decode($frame->data)->token;

            if (array_key_exists($token, $this->tokens)) { // Пользователь найден
                $server->push($frame->fd, $this->process_message($frame->data));
            } else {
                $user = User::where('api_token', $token)->first();

                if ($user) { // Пользователь найден, но ранее не был авторизован
                    $this->tokens[$token] = $user;
                    $server->push($frame->fd, $this->process_message($frame->data));
                } else {
                    $this->info('User auth error, closing connection');
                    $server->close($frame->fd);
                }
            }

            $this->info($this->tokens[$token]);
            $this->info("Received message: {$frame->data}");
        });

        $server->on('Close', function (Server $server, int $fd) {
            $this->info("Client {$fd} connection closed");
        });

        $server->on('Disconnect', function (Server $server, int $fd) {
            $this->info("Client {$fd} connection disconnected");
        });

        $server->start();

        return 0;
    }

    protected function process_message(string $message): string
    {
        $request = json_decode($message);
        $response = null;

        switch (mb_strtoupper($request->type ?? null)) {
            case 'MESSAGE':
                // @TODO проверка на право/возможность писать в канал
                if (array_key_exists($request->channel_id ?? null, $this->channels)) {
                    $response = new ChannelMessage(1, $request->channel_id, $request->message);
                } else {
                    $response = new ErrorMessage(406, 'Channel not found');
                }
                break;

            case 'REQUEST':
                switch (mb_strtoupper($request->target ?? null)) {
                    case 'CHANNEL_LIST':
                        $response = new ChannelList($this->channels);
                }

                break;

            default:
                $this->error("Incorrect message type");
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
