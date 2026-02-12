<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MovementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Schema::hasTable('movements')) {
            DB::table('movements')->insert([
                [
                    'user_id' => 1,
                    'category_id' => 1,
                    'sub_category_id' => 1,
                    'country_id' => 15,
                    'title' => 'Movement Title 1',
                    'description' => 'This is a sample movement description.',
                    'video' => 'https://youtu.be/KLuTLF3x9sA?si=ExZ9BYvB-QuH3DEA',
                    'pdf'  =>  'backend/images/screencapture-fiverr-orders-FO1136B554C45-activities-2025-03-17-08_22_46.pdf',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => 2,
                    'category_id' => 2,
                    'sub_category_id' => 2,
                    'country_id' => 18,
                    'title' => 'Movement Title 2',
                    'description' => 'This is another sample movement description.',
                    'video' => 'https://youtu.be/AB-4pS2Og1g?si=nbFeLIUhPzUHN-9i',
                    'pdf'  =>  'backend/images/screencapture-fiverr-orders-FO1136B554C45-activities-2025-03-17-08_22_46.pdf',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Schema::hasTable('posts')) {
            DB::table('posts')->insert([
                [
                    'user_id' => 1,
                    'sub_category_id' => 1,
                    'category_id' => 1,
                    'movement_id' => 1,
                    'title' => 'Movement Title 1',
                    'description' => 'This is a sample movement description.',
                    'video' => 'backend/images/1585-148613508.mp4',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => 2,
                    'sub_category_id' => 2,
                    'category_id' => 2,
                    'movement_id' => 2,
                    'title' => 'Movement Title 2',
                    'description' => 'This is another sample movement description.',
                    'video' => 'backend/images/1585-148613508.mp4',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
