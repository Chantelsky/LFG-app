<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            ['name' => 'Valorant', 'slug' => 'valorant'],
            ['name' => 'Apex Legends', 'slug' => 'apex-legends'],
            ['name' => 'League of Legends', 'slug' => 'league-of-legends'],
            ['name' => 'Overwatch 2', 'slug' => 'overwatch-2'],
            ['name' => 'Rocket League', 'slug' => 'rocket-league'],
            ['name' => 'CS2', 'slug' => 'cs2'],
        ];

        foreach ($games as $game) {
            Game::create($game);
        }
    }
}
