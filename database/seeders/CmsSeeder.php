<?php

namespace Database\Seeders;

use App\Models\Cms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cms::insert([
            [
                'id' => 1,
                'duration' => '15',
                'size' => '5120',
                'donation_amount' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
