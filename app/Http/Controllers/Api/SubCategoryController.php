<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class SubCategoryController extends Controller
{
    use ApiResponse;

    public function subcategory(Request $request)
    {
        $user = auth()->user();
        $lang = $user ? strtolower($user->language) : 'english';
        switch ($lang) {
            case 'english':
                $lang = 'en';
                break;
            case 'spanish':
                $lang = 'sp';
                break;
            case 'french':
                $lang = 'fr';
                break;
            case 'catalan':
                $lang = 'ca';
                break;
            default:
                $lang = 'en'; // Default to English if unsupported language
        }
        $search = $request->query('name');
        $categoryId = $request->query('category_id');

        // Determine the field names based on the language
        $categoryNameField = "{$lang}_category_name";
        $subcategoryNameField = "{$lang}_subcategory_name";

        // Validate supported languages
        $supportedLanguages = ['en', 'sp', 'fr', 'ca'];
        if (!in_array($lang, $supportedLanguages)) {
            return $this->error([], 'Unsupported language selected', 400);
        }

        if ($search) {
            $categories = Category::whereHas('subCategories', function ($q) use ($search, $lang) {
                $q->where("{$lang}_subcategory_name", 'LIKE', "%$search%");
            })
                ->with(['subCategories' => function ($q) use ($search, $lang) {
                    $q->where("{$lang}_subcategory_name", 'LIKE', "%$search%");
                }])
                ->get();
        } elseif ($categoryId) {
            $categories = Category::where('id', $categoryId)
                ->with('subCategories')
                ->get();
        } else {
            $categories = Category::with('subCategories')->get();
        }

        if ($categories->isEmpty()) {
            return $this->error([], 'No data found', 200);
        }

        $formatted = $categories->map(function ($cat) use ($categoryNameField, $subcategoryNameField) {
            return [
                'id' => $cat->id,
                'category_name' => $cat->$categoryNameField,
                'status' => $cat->status,
                'category_status' => $cat->status,
                'sub_categories' => $cat->subCategories->map(function ($sub) use ($subcategoryNameField) {
                    return [
                        'id' => $sub->id,
                        'category_id' => $sub->category_id,
                        'subcategory_name' => $sub->$subcategoryNameField,
                    ];
                }),
            ];
        });

        return $this->success($formatted, 'Data fetched successfully!', 200);
    }
}
