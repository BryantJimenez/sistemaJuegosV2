<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoupleGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('couple_game', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('couple_id')->unsigned();
            $table->bigInteger('game_id')->unsigned();
            $table->integer('points')->unsigned()->default(0);
            $table->timestamps();

            #Relations
            $table->foreign('couple_id')->references('id')->on('couples')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('couple_game');
    }
}
