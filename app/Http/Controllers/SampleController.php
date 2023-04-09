<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as ImageIntervention;
use SebastianBergmann\Environment\Runtime;
use App\Models\Image;
use App\Models\Place;
class SampleController extends Controller
{
    public function index()
    {
        return view('sample.index');
    }

    public function address(Request $request)
    {
        $places = Place::where('postal_code', $request->postal)->get();
        return response()->json($places);
    }

    public function cities(Request $request)
    {
        $cities = Place::where('prefecture', $request->prefecture)->select('city')->groupBy('city')->get();
        return response()->json($cities);
    }

    public function streets(Request $request)
    {
        $streets = Place::where('prefecture', $request->prefecture)->where('city', $request->city)->select('street')->groupBy('street')->get();
        return response()->json($streets);
    }

    public function sample3()
    {
        return view('sample.sample3', [
            'prefectures' => [
                "北海道",
                "青森県",
                "岩手県",
                "宮城県",
                "秋田県",
                "山形県",
                "福島県",
                "茨城県",
                "栃木県",
                "群馬県",
                "埼玉県",
                "千葉県",
                "東京都",
                "神奈川県",
                "新潟県",
                "富山県",
                "石川県",
                "福井県",
                "山梨県",
                "長野県",
                "岐阜県",
                "静岡県",
                "愛知県",
                "三重県",
                "滋賀県",
                "京都府",
                "大阪府",
                "兵庫県",
                "奈良県",
                "和歌山県",
                "鳥取県",
                "島根県",
                "岡山県",
                "広島県",
                "山口県",
                "徳島県",
                "香川県",
                "愛媛県",
                "高知県",
                "福岡県",
                "佐賀県",
                "長崎県",
                "熊本県",
                "大分県",
                "宮崎県",
                "鹿児島県",
                "沖縄県",
            ],
            // 'cities' => Place::where('prefecture', $this->prefecture)->select('city')->groupBy('city')->get(),
            // 'streets' => Place::where('prefecture', $this->prefecture)->where('city', $this->city)->select('street')->groupBy('street')->get(),
            // 'blocks' => Place::where('prefecture', $this->prefecture)->where('city', $this->city)->where('street', $this->street)->select('block')->get(),
        ]);
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
