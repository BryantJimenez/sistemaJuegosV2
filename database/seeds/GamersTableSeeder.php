<?php

use Illuminate\Database\Seeder;

class GamersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Gamer::class, 100)->create();
    }
}
