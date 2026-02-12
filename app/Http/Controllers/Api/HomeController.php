<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponse;

    public function __invoke()
    {
        $homePage = Banner::all();

        if ($homePage->isEmpty()) {
            return $this->error([], 'No data found', 200);
        }

        return $this->success($homePage, 'Data fetched successfully!', 200);
    }
}
