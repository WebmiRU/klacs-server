<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('channel_id');
            $table->bigInteger('user_id');
            $table->text('value');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')
                ->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->foreign('channel_id')->references('id')->on('channel')
                ->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message');
    }
};
