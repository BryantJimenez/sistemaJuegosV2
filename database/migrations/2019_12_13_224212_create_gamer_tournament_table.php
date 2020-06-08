<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamerTournamentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamer_tournament', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('gamer_id')->unsigned();
            $table->bigInteger('tournament_id')->unsigned();
            $table->bigInteger('couple_id')->unsigned()->nullable();
            $table->timestamps();

            #Relations
            $table->foreign('gamer_id')->references('id')->on('gamers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gamer_tournament');
    }
}
