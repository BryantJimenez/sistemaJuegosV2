<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoupleGamerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('couple_gamer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('gamer_id')->unsigned();
            $table->bigInteger('couple_id')->unsigned(); 
            $table->timestamps();

            #Relations
            $table->foreign('gamer_id')->references('id')->on('gamers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('couple_id')->references('id')->on('couples')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('couple_gamer');
    }
}
