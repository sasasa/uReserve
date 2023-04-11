<x-layouts.sample title="livewire">
<div>
    <div id="modal">
      <div id="modal-content">
        <h2>住所選択</h2>
        <p>郵便番号に対して複数の住所が該当します</p>
        <ul id="address_list" class="modal">
          @if ($places->count() > 1)
            @foreach ($places as $place)
              <li>
                <label>
                  <input type="radio" name="place" value="{{ $place->id }}">
                  {{ $place->prefecture.$place->city.$place->street }}
                </label>
              </li>
            @endforeach
          @endif
        </ul>
      </div>
    </div>
    <br>
    <div id="target"></div>
    <div id="address"></div>
    <input type="hidden" id="lat" name="lat">
    <input type="hidden" id="lng" name="lng">
    @if ($places->count() == 1)
      @foreach ($places as $place)
        <input type="hidden" id="prefecture" name="prefecture" value="{{ $place->prefecture }}">
        <input type="hidden" id="city" name="city" value="{{ $place->city }}">
        <input type="hidden" id="street" name="street" value="{{ $place->street }}">
      @endforeach
    @else
        <input type="hidden" id="prefecture" name="prefecture" value="">
        <input type="hidden" id="city" name="city" value="">
        <input type="hidden" id="street" name="street" value="">
    @endif
<style>
  #modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.5);
}

#modal-content {
  background-color: #fff;
  margin: 10% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  max-width: 600px;
}
#target {
    width: 550px;
    height: 200px;
}
</style>
<script async defer src="https://maps.googleapis.com/maps/api/js?language=ja&region=JP&key={{ env('GOOGLE_MAP_API_KEY') }}&callback=initMap"></script>
<script>
    const modal = document.getElementById('modal');
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
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

    function showMap() {
        if(document.getElementById('prefecture').value && document.getElementById('city').value && document.getElementById('street').value) {
            getMapByAddress()
        } else {
            showModal()
        }
    }

    function showModal() {
        const modal = document.getElementById('modal');
        modal.style.display = "block";
    }
    // input type="radio" name="place"がchangeされたら
    document.getElementById('address_list').addEventListener('change', function(e) {
        const place_id = e.target.value
        const modal = document.getElementById('modal');
        modal.style.display = "none";
        getMapByPlaceId(place_id)
    });

    function getMapByPlaceId(place_id) {
        const places = @json($places);
        console.log(places)
        const place = places.find(place => place.id == place_id)
        document.getElementById('prefecture').value = place.prefecture
        document.getElementById('city').value = place.city
        document.getElementById('street').value = place.street
        getMapByAddress()
    }


    function getMapByAddress() {
        let geocoder = new google.maps.Geocoder();
        // Geocoding Address->Latlng
        const address = document.getElementById('prefecture').value +
                document.getElementById('city').value +
                document.getElementById('street').value

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
                    getAddress(e.latLng)
                    google.maps.event.addListener(marker, 'dragend', function(ev){
                        // alert(ev.latLng.lat() + "," + ev.latLng.lng())
                        document.getElementById('lat').value = ev.latLng.lat()
                        document.getElementById('lng').value = ev.latLng.lng()
                        map.panTo(ev.latLng);
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
        showMap()
    }

</script>
</div>
</x-layouts.sample>