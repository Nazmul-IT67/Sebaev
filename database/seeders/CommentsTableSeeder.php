<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('comments')->insert([
            [
                'user_id' => 1,
                'movement_id' => 1,
                'post_id' => 1,
                'comment' => 'This is the first comment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'movement_id' => 2,
                'post_id' => 2,
                'comment' => 'Second comment here.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'movement_id' => 1,
                'post_id' => 1,
                'comment' => 'Third comment example.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
