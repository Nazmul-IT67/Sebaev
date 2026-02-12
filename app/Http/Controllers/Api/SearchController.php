<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'search'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $search = $request->query('search');

        $data = Movement::where('title', 'like', '%' . $search . '%')
            ->select(['id', 'user_id', 'category_id', 'sub_category_id', 'title', 'description', 'country'])
            ->with(['user:id,name,avatar']) // Optional: load creator info
            ->withCount(['userMovements', 'comments']) // Optional: count relations
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No movements found.', 200);
        }

        return $this->success($data, 'Movements fetched successfully.', 200);
    }
}
