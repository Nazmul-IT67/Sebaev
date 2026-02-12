<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::insert([
            [
                'id'             => 1,
                'logo'           => "logo.png",
                'favicon'        => "logo.png",
                'copyright_text' => 'Copyright © 2017 - 2024 DESIGN AND DEVELOPED BY ❤️',
                'created_at'     => Carbon::now(),
            ],
        ]);
    }
}
