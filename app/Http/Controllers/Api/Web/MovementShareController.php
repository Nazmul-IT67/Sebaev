<?php

namespace App\Http\Controllers\Api\Web;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MovementShare;

class MovementShareController extends Controller
{
    use ApiResponse;
    
    public function movementShare($id)
    {
       $user = auth()->user();

       if(!$user){
           return $this->success([], 'Unauthorized', 401);
       }

        $data = MovementShare::create([
           'user_id' => $user->id,
           'movement_id' => $id,
        ]);

       return $this->success($data, 'Success', 200);
    }
}
