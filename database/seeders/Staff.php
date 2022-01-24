<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class Staff extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'discord_id' => 171764626755813376,
            'telescope_access' => true,
        ]);
    }
}
