<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('groups')->unsigned()->default(6);
            $table->integer('couples')->unsigned()->default(6);
            $table->enum('type', [1, 2])->desault(1);
            $table->enum('state', [1, 2, 3])->default(1);
            $table->date('start');
            $table->date('end')->nullable();
            $table->bigInteger('club_id')->unsigned()->nullable();
            $table->timestamps();

            #Relations
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
}
