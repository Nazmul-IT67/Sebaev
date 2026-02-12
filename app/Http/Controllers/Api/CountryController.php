<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use ApiResponse;
    public function __invoke()
    {
        $data = Country::all();

        if ($data->isEmpty()) {
            return $this->error([], 'Country not found', 200);
        }

        return $this->success($data, 'Country fetch Successful!', 200);
    }
}
