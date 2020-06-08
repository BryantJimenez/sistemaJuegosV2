<?php

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
        $this->call(UsersTableSeeder::class);
        $this->call(PhasesTableSeeder::class);
        $this->call(GamersTableSeeder::class);
        $this->call(ClubsTableSeeder::class);
        $this->call(TournamentsTableSeeder::class);
    }
}
