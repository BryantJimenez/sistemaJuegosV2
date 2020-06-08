<?php

use Illuminate\Database\Seeder;

class PhasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	App\Phase::create(
    		['name' => 'Fase de Grupos', 'slug' => 'fase-de-grupos']
    	);
        App\Phase::create(
            ['name' => 'Semifinal', 'slug' => 'semifinal']
        );
        App\Phase::create(
            ['name' => 'Final', 'slug' => 'final']
        );
    }
}
