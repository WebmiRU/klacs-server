<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Channel::factory(10)->create();

        User::factory(10)->create()->each(function (User $user) {
            $user->channels()->sync([1, 2, 3, 4, 5]);
        });
    }
}
