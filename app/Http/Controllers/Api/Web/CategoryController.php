<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use ApiResponse;

    public function getCategories()
    {
        $user = auth()->user();
        $language = $user ? strtolower($user->language) : 'english';

        $data = Category::where('status', 'active')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Category not found', 200);
        }

        // Map the language to corresponding field
        $languageFieldMap = [
            'english' => 'en_category_name',
            'spanish' => 'sp_category_name',
            'french'  => 'fr_category_name',
            'catalan' => 'ca_category_name',
        ];

        $field = $languageFieldMap[$language] ?? 'en_category_name';

        // Transform data
        $transformed = $data->map(function ($item) use ($field) {
            return [
                'id'              => $item->id,
                'category_name'   => $item->$field,
                'status'          => $item->status,
                'category_status' => $item->category_status,
            ];
        });

        return $this->success($transformed, 'Data fetched successfully!', 200);
    }
}
