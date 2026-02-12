<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SocialMediaSeeder::class);
        $this->call(SystemSettingSeeder::class);
        $this->call(DynamicPageSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(MovementsSeeder::class);
        $this->call(BannerSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(CmsSeeder::class);
        $this->call(DonationHistorySeeder::class);
        $this->call(MovementDocumentSeeder::class);
        $this->call(CommentsTableSeeder::class);
    }
}
