<?php

namespace Database\Seeders;

use App\Models\DonationHistory;
use App\Models\Movement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DonationHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $movements = Movement::pluck('id')->toArray();

        if (empty($users) || empty($movements)) {
            $this->command->warn('No users or movements found. Please seed them first.');
            return;
        }

        foreach (range(1, 20) as $i) {
            DonationHistory::create([
                'user_id' => fake()->randomElement($users),
                'movement_id' => fake()->randomElement($movements),
                'amount' => fake()->randomFloat(2, 10, 1000),
            ]);
        }
    }
}
