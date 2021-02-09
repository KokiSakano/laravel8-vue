<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\Whisper;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Whisper::factory()->count(15)->create();
    }
}
