<?php

use Illuminate\Database\Seeder;

class TournamentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Tournament::create(
    		['name' => 'Torneo normal', 'slug' => 'torneo-normal', 'groups' => 4, 'type' => 1, 'start' => '2019-12-16']
    	);
        App\Tournament::create(
            ['name' => 'Tornel de clubes', 'slug' => 'torneo-de-clubes', 'groups' => 5, 'type' => 2, 'start' => '2019-12-16']
        );
        App\Tournament::create(
            ['name' => 'Torneo para probar', 'slug' => 'torneo-para-probar', 'groups' => 3, 'type' => 1, 'state' => 1, 'start' => '2019-12-16']
        );
    }
}
