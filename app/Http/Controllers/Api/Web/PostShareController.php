<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\PostShare;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostShareController extends Controller
{

    use ApiResponse;
    
    public function postShare($id)
    {
        $user = auth()->user();

        if(!$user){
            return $this->success([], 'Unauthorized', 401);
        }

        // $share = PostShare::where('user_id', $user->id)->where('post_id', $id)->first();

        // if($share){
        //     return $this->success([], 'Already shared', 200);
        // }

        $data = PostShare::create([
            'user_id' => $user->id,
            'post_id' => $id,
        ]);

        return $this->success($data, 'Success', 200);
    }
}
