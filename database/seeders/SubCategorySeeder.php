<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Ensure there are categories before seeding subcategories
         $categories = Category::all();

         if ($categories->isEmpty()) {
             // Optionally, seed some categories if none exist
             $categories = Category::factory()->count(3)->create();
         }

         $subCategories = [
             ['en_subcategory_name' => 'Social Activities', 'sp_subcategory_name' => 'Actividades sociales', 'fr_subcategory_name' => 'Activités sociales', 'ca_subcategory_name' => 'Activitats socials', 'status' => 'active'],
             ['en_subcategory_name' => 'Fitness & Health','sp_subcategory_name' => 'Salud y Fitness', 'fr_subcategory_name' => 'Santé et Fitness', 'ca_subcategory_name' => 'Salut i Fitness', 'status' => 'active'],
             ['en_subcategory_name' => 'Art & Culture', 'sp_subcategory_name' => 'Arte y Cultura', 'fr_subcategory_name' => 'Art et Culture', 'ca_subcategory_name' => 'Arte i Cultura', 'status' => 'inactive'],
             ['en_subcategory_name' => 'Travel & Outdoor', 'sp_subcategory_name' => 'Viajes y Outdoor', 'fr_subcategory_name' => 'Voyages et Outdoor', 'ca_subcategory_name' => 'Viajat i Outdoor', 'status' => 'active'],
             ['en_subcategory_name' => 'Learn', 'sp_subcategory_name' => 'Aprender', 'fr_subcategory_name' => 'Apprendre', 'ca_subcategory_name' => 'Aprendre', 'status' => 'inactive'],
             ['en_subcategory_name' => 'Artificial Intelligence', 'sp_subcategory_name' => 'Inteligencia Artificial', 'fr_subcategory_name' => 'Intelligence Artificielle', 'ca_subcategory_name' => 'Inteligencia Artificial', 'status' => 'active'],
         ];

         foreach ($categories as $category) {
             foreach ($subCategories as $subCategory) {
                 SubCategory::create([
                     'category_id' => $category->id,
                     'en_subcategory_name' => $subCategory['en_subcategory_name'],
                     'sp_subcategory_name' => $subCategory['sp_subcategory_name'],
                     'fr_subcategory_name' => $subCategory['fr_subcategory_name'],
                     'ca_subcategory_name' => $subCategory['ca_subcategory_name'],
                     'status' => $subCategory['status'],
                 ]);
             }
         }
    }
}