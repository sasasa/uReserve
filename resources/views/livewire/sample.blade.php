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
    <form wire:submit.prevent="confirm">
        @error('mapImage') <div class="error">{{ $message }}</div> @enderror

        <input type="hidden" id="lat" wire:model="lat">
        <input type="hidden" id="lng" wire:model="lng">
        @error('lat') <div class="error">{{ $message }}</div> @enderror

        <input type="text" id="postalCode" wire:model="postalCode">
        @error('postalCode') <div class="error">{{ $message }}</div> @enderror

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
        
        <select name="street" id="street">
            <option value="">選択してください</option>
            @foreach ($streets as $place)
                @if(!empty($place->street))
                    <option @selected(session('street', $street) == $place->street) value="{{ $place->street }}">{{ $place->street }}</option>
                @endif
            @endforeach
        </select>
        @error('street') <div class="error">{{ $message }}</div> @enderror

        <input type="text" list="block" id="block_info" value="{{ session('block') }}">
        <datalist name="block" id="block">
            <option value="">選択してください</option>
            @foreach ($blocks as $place)
                @if(!empty($place->block))
                    <option value="{{ $place->block }}">{{ $place->block }}</option>
                @endif
            @endforeach
        </datalist>
        @error('block') <div class="error">{{ $message }}</div> @enderror

        <div>
            <button onclick="setData()" wire:target="search" wire:loading.attr="disabled" type="submit" class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition'>Save</button>
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
    }
    function dispatchEventToBlockInfo() {
        const ev = new Event("change", {
            bubbles: false,
            cancelable: true
        })
        document.getElementById('block_info').dispatchEvent(ev);
    }
    document.addEventListener('mapShow', function () {
        getMapByAddress()
    });
    document.getElementById('street').addEventListener('change',function(){
        getMapByAddress()
    })
    document.getElementById('block_info').addEventListener('change',function(){
        getMapByAddress()
    })

    function setData() {
        @this.set('lat', document.getElementById('lat').value);
        @this.set('lng', document.getElementById('lng').value);
        @this.set('street', document.getElementById('street').value);
        @this.set('block', document.getElementById('block_info').value);
        @this.set('mapImage', mapImage);
    }
    const target = document.getElementById('target');
    let map = null;
    let marker = [];
    function getMapByAddress() {
        if(document.getElementById('prefecture').value == "" ||
            document.getElementById('city').value == "" ||
            document.getElementById('street').value == "" ||
            document.getElementById('block_info').value == "") 
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
                });

                map.addListener('click', function(e) {
                    marker.forEach(function(m) {
                        m.setMap(null);
                    });
                    marker = [];
                    document.getElementById('lat').value = e.latLng.lat()
                    document.getElementById('lng').value = e.latLng.lng()
                    this.panTo(e.latLng);
                    marker.push(new google.maps.Marker({
                        position: e.latLng,
                        map: map,
                        title: e.latLng.toString(),
                        animation: google.maps.Animation.DROP,
                    }));
                    map2image()
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
            });
            marker.push(new google.maps.Marker({
                position: center,
                map: map,
                animation: google.maps.Animation.DROP,
            }));
            map2image()
            map.addListener('click', function(e) {
                marker.forEach(function(m) {
                    m.setMap(null);
                });
                marker = [];
                document.getElementById('lat').value = e.latLng.lat()
                document.getElementById('lng').value = e.latLng.lng()
                this.panTo(e.latLng);
                marker.push(new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                    title: e.latLng.toString(),
                    animation: google.maps.Animation.DROP,
                }));
                map2image()
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