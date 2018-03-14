<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLobbyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lobby_user', function (Blueprint $table) {
            $table->unsignedInteger('lobby_id')->references('id')->on('lobbies');
            $table->unsignedInteger('user_id')->references('id')->on('users');
            $table->boolean('is_ready')->default(false);
            $table->string('is_spectator')->default(false);
            $table->unique(['lobby_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lobby_user');
    }
}
