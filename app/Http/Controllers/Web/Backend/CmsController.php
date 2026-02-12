<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Cms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CmsController extends Controller
{
    public function index()
    {
        $data = Cms::latest('id')->first();
        return view('backend.layouts.duration.index', compact('data'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'duration'          => 'required|numeric',
            'size'          => 'required|numeric',
            'donation_amount'    => 'required|numeric',
            
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $data                 = Cms::firstOrNew();
            $data->duration       = $request->duration;
            $data->size           = $request->size;
            $data->donation_amount    = $request->donation_amount;

            $data->save();
            return back()->with('t-success', 'Updated successfully');
        } catch (\Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
