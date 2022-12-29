<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as ImageIntervention;
use SebastianBergmann\Environment\Runtime;

class SampleController extends Controller
{
    public function index()
    {
        return view('sample.index');
    }

    public function sample2(Request $request)
    {
        if($request->isMethod('post')){
            // dd(session('lat'));
            // PHPから画像のパーミッションを変更数する
            $path = storage_path(session('lat'). '-'. session('lng'). '.jpg');
            // chmod($path, 0777);
            if(file_exists($path)) {
                $mapImage = ImageIntervention::make($path);
            } else {
                throw new \RuntimeException('画像が存在しません'.$path);
            }
            // $mapImage = ImageIntervention::make(storage_path(session('lat')."-".session('lng'). '.jpg'));
            session()->flush();
            return $mapImage->response('jpg');
        }
        return view('sample.sample2');
    }
}
