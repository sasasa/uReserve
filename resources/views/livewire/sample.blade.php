<div>
    @if (session()->has('message'))
    <div>
        {{ session('message') }}
    </div>
    @endif
    <div wire:loading.delay wire:target="save">
        Processing ...
    </div>

    <div id="target"></div>
    <div id="target2"></div>
    <div id="address"></div>
    <form wire:submit.prevent="confirm">
        @if(count($errors) > 0)
        <div class="brdBox indent error colorRed mb20">
        <strong>入力エラーがございます。赤くエラーになった欄を正しく入力してください。</strong>
        @foreach (collect($errors->all())->unique() as $error)
            <p><i class="fas fa-exclamation-circle"></i> <strong>{{ $error }}</strong></p>
        @endforeach
        </div>
        @endif

        @error('mapImage') <div class="error">{{ $message }}</div> @enderror

        <input type="hidden" id="lat" wire:model="lat">
        <input type="hidden" id="lng" wire:model="lng">
        @error('lat') <div class="error">{{ $message }}</div> @enderror

        <input
        maxlength="7" oninput="value = value.replace(/[^0-9]+/i,'');"
        type="text" id="postalCode" wire:model="postalCode">
        @error('postalCode') <div class="error">{{ $message }}</div> @enderror

        @if(count($places) > 1)
            <ul>
                @foreach ($places as $place)
                <li wire:click="setPlace('{{ $place['postal_code'] }}', '{{ $place['prefecture'] }}','{{ $place['city'] }}','{{ $place['street'] }}')">
                    {{ $place['prefecture'] }}
                    {{ $place['city'] }}
                    {{ $place['street'] }}
                </li>
                @endforeach
            </ul>
        @endif

        <select name="prefecture" id="prefecture" wire:model="prefecture">
            <option value="">選択してください</option>
            @foreach ($prefectures as $prefecture)
            {{-- @selected() --}}
            <option value="{{ $prefecture }}">{{ $prefecture }}</option>
            @endforeach
        </select>
        @error('prefecture') <div class="error">{{ $message }}</div> @enderror

        <select name="city" id="city" wire:model="city">
            <option value="">選択してください</option>
            @foreach ($cities as $place)
            <option value="{{ $place->city }}">{{ $place->city }}</option>
            @endforeach
        </select>
        @error('city') <div class="error">{{ $message }}</div> @enderror
        
        {{-- <select name="street" id="street">
            <option value="">選択してください</option>
            @foreach ($streets as $place)
                @if(!empty($place->street))
                    <option @selected(session('street', $street) == $place->street) value="{{ $place->street }}">{{ $place->street }}</option>
                @endif
            @endforeach
        </select>
        @error('street') <div class="error">{{ $message }}</div> @enderror --}}
        <input type="text" list="street_info" id="street" value="{{ session('street', $street) }}">
        <datalist name="street" id="street_info">
            @foreach ($streets as $place)
                <option @selected(session('street', $street) == $place->street) value="{{ $place->street }}">{{ $place->street }}</option>
            @endforeach
        </datalist>

        <input type="text" list="block_info" id="block" value="{{ session('block') }}">
        <datalist name="block" id="block_info">
            <option value="">選択してください</option>
            @foreach ($blocks as $place)
                @if(!empty($place->block))
                    <option value="{{ $place->block }}">{{ $place->block }}</option>
                @endif
            @endforeach
        </datalist>
        @error('block') <div class="error">{{ $message }}</div> @enderror

        <div>
            <button onclick="return setData();" wire:target="search" wire:loading.attr="disabled" type="submit" class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition'>Save</button>
        </div>
    </form>
    

    {{-- <form wire:submit.prevent="save">
        @if ($photo)
            <div>
                Photo Preview:
                <img style="object-fit: cover;width: 100px; height: 100px;" src="{{ $photo->temporaryUrl() }}">
            </div>
        @endif

        <div wire:loading wire:target="photo">Uploading...</div>
        <div>
            <input type="file" wire:model="photo" accept="image/png, image/jpeg, image/gif">
        </div>
        @error('photo') <div class="error">{{ $message }}</div> @enderror
        <div>
            <input type="text" wire:model="alt">
        </div>
        @error('alt') <div class="error">{{ $message }}</div> @enderror
        <div>
            <button @disabled($is_button_disabled) wire:target="save" wire:loading.attr="disabled" type="submit" class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition'>Save Photo</button>
        </div>
    </form> --}}

    {{-- <div>
        <div style="display: flex; justify-content: start;">
            {{ $images->links() }}
        </div>
        <div style="display: flex;">
        @forelse ($images as $image)
            <img style="object-fit: cover;width: 100px; height: 100px;" alt="{{ $image->alt }}" src="/storage/{{ $image->file_name }}">
        @empty
            空です。
        @endforelse
        </div>
    </div> --}}
    <div>
        <img src="" id="ss" alt="">
    </div>
<style>
#target {
    width: 550px;
    height: 200px;
}
#target2 {
    width: 550px;
    height: 200px;
}
</style>
<script async defer src="https://maps.googleapis.com/maps/api/js?language=ja&region=JP&key={{ env('GOOGLE_MAP_API_KEY') }}&callback=initMap"></script>
<script>
    let mapImage = "";
    function map2image() {
        setTimeout(() => {
            html2canvas(document.getElementById('target'), {
                useCORS: true,
            }).then(function (canvas) {
                mapImage = canvas.toDataURL();
            });
        }, 200);
        // .catch(function (error) {
        //     mapImage = ""
        //     console.error('oops, something went wrong!', error)
        // });
    }
    function dispatchEventToBlockInfo() {
        const ev = new Event("change", {
            bubbles: false,
            cancelable: true
        })
        document.getElementById('block').dispatchEvent(ev);
    }
    document.addEventListener('mapShow', function () {
        getMapByAddress()
    });
    document.getElementById('street').addEventListener('change',function(){
        getMapByAddress()
    })
    document.getElementById('block').addEventListener('change',function(){
        getMapByAddress()
    })

    function setData() {
        @this.set('mapImage', mapImage);
        @this.set('lat', document.getElementById('lat').value);
        @this.set('lng', document.getElementById('lng').value);
        @this.set('street', document.getElementById('street').value);
        @this.set('block', document.getElementById('block').value);
        return true;
    }
    function getAddress(latLng) {
        //Google Maps APIのジオコーダを使います。
        const geocoder = new google.maps.Geocoder();
        
        //ジオコーダのgeocodeを実行します。
        //第１引数のリクエストパラメータにlatLngプロパティを設定します。
        //第２引数はコールバック関数です。取得結果を処理します。
        geocoder.geocode(
            {
                latLng: latLng
            },
            function(results, status) {
            let address = "";
            if (status == google.maps.GeocoderStatus.OK) {
                //取得が成功した場合
                //住所を取得します。
                address = results[0].formatted_address;
            } else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                alert("住所が見つかりませんでした。");
            } else if (status == google.maps.GeocoderStatus.ERROR) {
                alert("サーバ接続に失敗しました。");
            } else if (status == google.maps.GeocoderStatus.INVALID_REQUEST) {
                alert("リクエストが無効でした。");
            } else if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                alert("リクエストの制限回数を超えました。");
            } else if (status == google.maps.GeocoderStatus.REQUEST_DENIED) {
                alert("サービスが使えない状態でした。");
            } else if (status == google.maps.GeocoderStatus.UNKNOWN_ERROR) {
                alert("原因不明のエラーが発生しました。");
            }
            //addressに住所の結果表示をします。
            document.getElementById('address').innerHTML = address;
        });
    }

    const target = document.getElementById('target');
    let map = null;
    let markers = [];
    function getMapByAddress() {
        if(document.getElementById('prefecture').value == "" ||
            document.getElementById('city').value == "" ||
            document.getElementById('street').value == "" ||
            document.getElementById('block').value == "") 
        {
            return;
        }

        let geocoder = new google.maps.Geocoder();
        // Geocoding Address->Latlng
        const address = document.getElementById('prefecture').value +
                document.getElementById('city').value +
                document.getElementById('street').value +
                document.getElementById('block').value

        geocoder.geocode({
            address: address
            // address: "〒1400001"
        }, 
        function(results, status) {
            if(status !== "OK") {
                alert('Mapを取得できません:' + status);
                return;
            }
            if(results[0]) {
                document.getElementById('lat').value = ""
                document.getElementById('lng').value = ""

                const location = results[0].geometry.location;
                map = new google.maps.Map(target, {
                    center: location,
                    zoom: 18,
                    disableDefaultUI: true,
                    scrollwheel: false,
                    disableDoubleClickZoom: true,
                });

                map.addListener('click', function(e) {
                    markers.forEach(function(m) {
                        m.setMap(null);
                    });
                    markers = [];
                    document.getElementById('lat').value = e.latLng.lat()
                    document.getElementById('lng').value = e.latLng.lng()
                    this.panTo(e.latLng);
                    const marker = new google.maps.Marker({
                        position: e.latLng,
                        map: map,
                        title: e.latLng.toString(),
                        animation: google.maps.Animation.DROP,
                        draggable: true,
                    })
                    markers.push(marker);
                    map2image()
                    getAddress(e.latLng)
                    google.maps.event.addListener(marker, 'dragend', function(ev){
                        // alert(ev.latLng.lat() + "," + ev.latLng.lng())
                        document.getElementById('lat').value = ev.latLng.lat()
                        document.getElementById('lng').value = ev.latLng.lng()
                        map.panTo(ev.latLng);
                        map2image()
                        getAddress(ev.latLng)
                    });
                });
            } else {
                alert('Mapを取得できません:' + results);
                return;
            }
        });
    }

    function initMap() {
        @if(session('lat'))
            let center = { lat: {{ session('lat') }}, lng: {{ session('lng') }} };

            map = new google.maps.Map(target, {
                center: center,
                zoom: 18,
                disableDefaultUI: true,
                clickableIcons: false,
                scrollwheel: false,
                disableDoubleClickZoom: true,
            });
            const marker = new google.maps.Marker({
                position: center,
                map: map,
                animation: google.maps.Animation.DROP,
                draggable: true,
            })
            markers.push(marker);
            map2image()

            google.maps.event.addListener(marker, 'dragend', function(ev){
                // alert(ev.latLng.lat() + "," + ev.latLng.lng())
                document.getElementById('lat').value = ev.latLng.lat()
                document.getElementById('lng').value = ev.latLng.lng()
                map.panTo(ev.latLng);
                map2image()
                getAddress(ev.latLng)
            });
            map.addListener('click', function(e) {
                markers.forEach(function(m) {
                    m.setMap(null);
                });
                markers = [];
                document.getElementById('lat').value = e.latLng.lat()
                document.getElementById('lng').value = e.latLng.lng()
                this.panTo(e.latLng);
                const marker = new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                    title: e.latLng.toString(),
                    animation: google.maps.Animation.DROP,
                    draggable: true,
                })
                markers.push(marker);
                map2image()
                google.maps.event.addListener(marker, 'dragend', function(ev){
                    // alert(ev.latLng.lat() + "," + ev.latLng.lng())
                    document.getElementById('lat').value = ev.latLng.lat()
                    document.getElementById('lng').value = ev.latLng.lng()
                    map.panTo(ev.latLng);
                    map2image()
                    getAddress(ev.latLng)
                });
            });
        @endif
    }

    // document.addEventListener('livewire:load', function() {
    //     function displayMapIfItIsInVariousValues() {
    //         if (!map && @this.postalCode && @this.prefecture && @this.city && @this.street && @this.block) {
    //             getMapByAddress();
    //         } else {
    //             map = null;
    //         }
    //         setTimeout(() => {
    //             displayMapIfItIsInVariousValues()
    //         },  330);
    //     }
    //     displayMapIfItIsInVariousValues()
    // })
</script>
</div>