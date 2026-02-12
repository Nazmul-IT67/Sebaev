<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MovementDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('movement_response_videos')->insert([
            [
                'movement_id' => 2,
                'user_id'     => 1,
                'file_url'    => 'https://youtu.be/w6uX9jamcwQ?si=1GQq9Ry86mOkcko-',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'movement_id' => 2,
                'user_id'     => 2,
                'file_url'    => 'https://youtu.be/w6uX9jamcwQ?si=1GQq9Ry86mOkcko-',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'movement_id' => 2,
                'user_id'     => 3,
                'file_url'    => 'https://youtu.be/w6uX9jamcwQ?si=1GQq9Ry86mOkcko-',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ]);
    }
}
