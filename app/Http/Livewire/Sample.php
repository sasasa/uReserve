<?php

namespace App\Http\Livewire;

use GuzzleHttp\Promise\Create;
use Livewire\WithFileUploads;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Image;
use App\Models\Place;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as ImageIntervention;

class Sample extends Component
{
    use WithFileUploads, WithPagination;

    public $photo;
    public string $alt = "";

    public string $postalCode = "";
    public string $prefecture = "";
    public string $city = "";
    public string $street = "";
    public string $block = "";
    public array $places = [];

    public string $lat = "";
    public string $lng = "";
    public string $mapImage = "";

    public bool $is_button_disabled = true;

    public function mount()
    {
        $this->postalCode = session('postalCode', '');
        $this->prefecture = session('prefecture', '');
        $this->city = session('city', '');
        $this->street = session('street', '');
        $this->block = session('block', '');
        $this->lat = session('lat', '');
        $this->lng = session('lng', '');
        $this->mapImage = session('mapImage', '');
        $this->is_button_disabled = $this->postalCode && $this->prefecture && $this->city && $this->street && $this->block ? false : true;
    }
    public function updatedPrefecture()
    {
        $this->city = "";
        $this->street = "";
        $this->block = "";
        $this->lat = "";
        $this->lng = "";
        $this->mapImage = "";
    }
    public function updatedCity()
    {
        $this->street = "";
        $this->block = "";
        $this->lat = "";
        $this->lng = "";
        $this->mapImage = "";
    }
    public function updatedStreet()
    {
        $this->block = "";
        // $this->lat = "";
        // $this->lng = "";
        // $this->mapImage = "";
        if($this->prefecture && $this->city && $this->street) {
            $p = Place::where('prefecture', $this->prefecture)->where('city', $this->city)->where('street', $this->street)->select('postal_code', 'prefecture', 'city', 'street')->first();
            // $this->postalCode = $p?->postal_code;
        }
    }
    public function updatedBlock()
    {
        $this->is_button_disabled = $this->postalCode && $this->prefecture && $this->city && $this->street && $this->block ? false : true;
    }

    public function setPlace($postal_code, $prefecture, $city, $street)
    {
        $this->postalCode = $postal_code;
        $this->prefecture = $prefecture;
        $this->city = $city;
        $this->street = $street;
        $this->block = "";
        $this->lat = "";
        $this->lng = "";
        $this->mapImage = "";
        $this->dispatchBrowserEvent('mapShow');
    }

    public function updatedPostalCode()
    {
        $this->postalCode = preg_replace('/[^0-9]/', '', mb_convert_kana($this->postalCode, 'a', 'UTF-8'));
        if (preg_match("/^\d{3}\d{4}$/", $this->postalCode)) {
            $places = Place::where('postal_code', $this->postalCode)->select('postal_code', 'prefecture', 'city', 'street')->get();
            if($places->count() > 1) {
                $this->places = $places->toArray();
                $this->prefecture = "";
                $this->city = "";
                $this->street = "";
                $this->block = "";
                $this->lat = "";
                $this->lng = "";
                $this->mapImage = "";
                return;
            }
            $place = $places->first();
            if($place) {
                $this->prefecture = $place->prefecture;
                $this->city = $place->city;
                session()->forget('street');
                $this->street = $place->street;
                $this->lat = "";
                $this->lng = "";
                $this->mapImage = "";
                if($places->count() == 1) {
                    $this->block = $place->block ?? "";
                }
                $this->dispatchBrowserEvent('mapShow');
            } else {
                $this->prefecture = "";
                $this->city = "";
                $this->street = "";
                $this->block = "";
                $this->lat = "";
                $this->lng = "";
                $this->mapImage = "";
            }
        } else {
            $this->prefecture = "";
            $this->city = "";
            $this->street = "";
            $this->block = "";
            $this->lat = "";
            $this->lng = "";
            $this->mapImage = "";
        }
    }

    public function confirm()
    {
        // dd('search');
        $this->dispatchBrowserEvent('mapShow');
        $this->validate([
            'postalCode' => ['required', 'max:50',],
            'prefecture' => ['required', 'max:50',],
            'city' => ['required', 'max:255',],
            'street' => ['required', 'max:255',],
            'block' => ['nullable', 'max:255',],
            'lat' => ['required', ],
            'lng' => ['required', ],
            'mapImage' => ['required', ],
        ], [
            'lat.required' => 'GoogleMap上の自宅の場所をクリックしてください',
        ]);

        session([
            'postalCode' => $this->postalCode,
            'prefecture' => $this->prefecture,
            'city' => $this->city,
            'street' => $this->street,
            'block' => $this->block,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ]);

        $path = storage_path(session('lat')."-".session('lng'). '.jpg');
        if(file_exists($path)) {
            $mapImage = ImageIntervention::make($path);
            return redirect()->route('sample2');
        } else {
            // dd($this->mapImage);
            $mapImage = ImageIntervention::make($this->mapImage);
        }
        $circle = ImageIntervention::make(public_path('images/circle.png'));
        $circle->widen(30);
        $position = 'center';
        $x = 0;
        $y = 0;
        $mapImage->insert($circle, $position, $x, $y);
        $mapImage->save(storage_path(session('lat')."-".session('lng'). '.jpg'));

        return redirect()->route('sample2');
    }

    public function save()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 最大１ＭＢ
            'alt' => ['required','min:5','max:50',],
        ]);
        Image::create([
            'alt' => $this->alt,
            'file_name' => str_replace('public', '', $this->photo->store('public/photos')),
        ]);
        // $this->resetPage();
        $this->photo = null;
        $this->alt = "";
        usleep(1400000);
        session()->flash("message", "画像を登録しました");
    }

    public function render()
    {
        return view('livewire.sample', [
            'images' => Image::paginate(3),
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
            'cities' => Place::where('prefecture', $this->prefecture)->select('city')->groupBy('city')->get(),
            'streets' => Place::where('prefecture', $this->prefecture)->where('city', $this->city)->select('street')->groupBy('street')->get(),
            'blocks' => Place::where('prefecture', $this->prefecture)->where('city', $this->city)->where('street', $this->street)->select('block')->get(),
        ]);
    }
}
