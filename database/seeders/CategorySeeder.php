<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            ['en_category_name' => 'Technology', 'sp_category_name' => 'Tecnología', 'fr_category_name' => 'Technologie', 'ca_category_name' => 'Tecnologia','status' => 'active', 'category_status' => 'active'],
            ['en_category_name' => 'Health','sp_category_name' => 'Salud', 'fr_category_name' => 'Santé', 'ca_category_name' => 'Salut', 'status' => 'active', 'category_status' => 'inactive'],
            ['en_category_name' => 'Education', 'sp_category_name' => 'Educación', 'fr_category_name' => 'Éducation', 'ca_category_name' => 'Educacio', 'status' => 'inactive', 'category_status' => 'inactive'],
            ['en_category_name' => 'Business', 'sp_category_name' => 'Negocios', 'fr_category_name' => 'Business', 'ca_category_name' => 'Business', 'status' => 'active', 'category_status' => 'inactive'],
            ['en_category_name' => 'Entertainment','sp_category_name' => 'Entretenimiento', 'fr_category_name' => 'Divertissement', 'ca_category_name' => 'Divertiment', 'status' => 'inactive', 'category_status' => 'inactive'],
        ];

        DB::table('categories')->insert($categories);
    }
}
