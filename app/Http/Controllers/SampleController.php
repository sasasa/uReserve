<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SampleController extends Controller
{
    public function index()
    {
        return view('sample.index');
    }

    public function sample2(Request $request)
    {
        if($request->isMethod('post')){
            dd(session('lat'));
        }
        return view('sample.sample2');
    }
}
