<x-layouts.sample title="確認画面">
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

</style>
<div>
  <div id="modal">
    <div id="modal-content">
      <h2>住所選択</h2>
      <p>郵便番号に対して複数の住所が該当します</p>
      <ul id="address_list" class="modal">
      </ul>
    </div>
  </div>

  <div>
    〒<input maxlength="7" 
    oninput="value = value.replace(/[^0-9]+/i,'');"
    type="text" name="postal" id="postal">
    <button type="button" id="modal-open">郵便番号から住所を取得</button>
  
    <select name="prefecture" id="prefecture">
      <option value="">選択してください</option>
      @foreach ($prefectures as $prefecture)
        <option value="{{ $prefecture }}">{{ $prefecture }}</option>
      @endforeach
    </select>
    <select name="city" id="city">
      <option value="">選択してください</option>
    </select>
    <select name="street" id="street">
      <option value="">選択してください</option>
    </select>
  </div>

  <hr>

  <div>
    〒<input maxlength="7" 
    oninput="value = value.replace(/[^0-9]+/i,'');"
    type="text" name="postal2" id="postal2">
    <button type="button" id="modal-open2">郵便番号から住所を取得</button>
  
    <select name="prefecture2" id="prefecture2">
      <option value="">選択してください</option>
      @foreach ($prefectures as $prefecture)
        <option value="{{ $prefecture }}">{{ $prefecture }}</option>
      @endforeach
    </select>
    <select name="city2" id="city2">
      <option value="">選択してください</option>
    </select>
    <select name="street2" id="street2">
      <option value="">選択してください</option>
    </select>
  </div>

  <script>
    let city_set = "";
    let street_set = "";

    function setAddress(prefectureId = 'prefecture', prefecture, city, street)
    {
        const pref = document.getElementById(prefectureId);  
        pref.value = prefecture;
        // prefectureのeventを発生させる
        city_set = city;
        street_set = street;
        pref.dispatchEvent(new Event('change'));
    }
    const modal = document.getElementById('modal');
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    
    const modal_open = document.getElementById('modal-open');
    const modal_open2 = document.getElementById('modal-open2');

    function setPlace(postalId = 'postal', prefectureId = 'prefecture', cityId = 'city', streetId = 'street')
    {
        const postal = document.getElementById(postalId);
        // 7桁の数字の正規表現じゃなかったら処理を終了
        if(!postal.value.match(/^\d{7}$/)) {
          return;
        }
        axios.post('{{ route('address') }}', {
            postal: postal.value
        }).then(function(response) {
          const address_list = document.getElementById('address_list');
          address_list.innerHTML = '';
          const prefecture = document.getElementById(prefectureId);
          const city = document.getElementById(cityId);
          const street = document.getElementById(streetId);
          prefecture.value = '';
          city.value = '';
          street.value = '';

          if(response.data.length == 1) {
            response.data.forEach(function(place) {
              setAddress(prefectureId, place['prefecture'], place['city'], place['street'])
            });
          } else {
            const modal = document.getElementById('modal');
            modal.style.display = 'block';
            const address_list = document.getElementById('address_list');
            response.data.forEach(function(place) {
              const li = document.createElement('li');
              const label = document.createElement('label');
              const input = document.createElement('input');
              input.type = 'radio';
              input.name = 'place';
              input.value = place['prefecture'] + place['city'] + place['street'];
              label.appendChild(input);
              label.appendChild(document.createTextNode(place['prefecture'] + place['city'] + place['street']));
              li.appendChild(label);
              address_list.appendChild(li);
              input.addEventListener('change', function() {
                setAddress(prefectureId, place['prefecture'], place['city'], place['street'])
                modal.style.display = 'none';
              });
            });
          }
        }).catch(function(error) {
          // console.log(error);
        });
    }
    // 変更されたら発生するイベント
    modal_open.addEventListener('click', function() {
        setPlace('postal', 'prefecture', 'city', 'street');
    });
    modal_open2.addEventListener('click', function() {
        setPlace('postal2', 'prefecture2', 'city2', 'street2');
    });

    function setCity(prefectureId = 'prefecture', streetId = 'street', cityId = 'city')
    {
        const prefecture = document.getElementById(prefectureId);
        axios.post('{{ route('cities') }}', {
            prefecture: prefecture.value
        }).then(function(response) {
          // console.log(response.data);
          const street = document.getElementById(streetId);
          street.innerHTML = '';
          const opt = document.createElement('option');
          opt.value = '';
          opt.text = '選択してください';
          street.appendChild(opt);

          const city = document.getElementById(cityId);
          city.innerHTML = '';
          const option = document.createElement('option');
          option.value = '';
          option.text = '選択してください';
          city.appendChild(option);
          response.data.forEach(function(cityName) {
            const option = document.createElement('option');
            option.value = cityName['city'];
            option.text = cityName['city'];
            city.appendChild(option);
          });
          if(city_set != "") {
            city.value = city_set;
            city_set = "";
            city.dispatchEvent(new Event('change'));
          }
        }).catch(function(error) {
          // console.log(error);
        });
    }
    const prefecture = document.getElementById('prefecture');
    const prefecture2 = document.getElementById('prefecture2');
    prefecture.addEventListener('change', function() {
        setCity('prefecture', 'street', 'city');
    });
    prefecture2.addEventListener('change', function() {
        setCity('prefecture2', 'street2', 'city2');
    });

    function setStreet(prefectureId = 'prefecture', cityId = 'city', streetId = 'street')
    {
        const prefecture = document.getElementById(prefectureId);
        const city = document.getElementById(cityId);
        axios.post('{{ route('streets') }}', {
            prefecture: prefecture.value,
            city: city.value
        }).then(function(response) {
          // console.log(response.data);
          const street = document.getElementById(streetId);
          street.innerHTML = '';
          const option = document.createElement('option');
          option.value = '';
          option.text = '選択してください';
          street.appendChild(option);
          response.data.forEach(function(streetName) {
            const option = document.createElement('option');
            option.value = streetName['street'];
            option.text = streetName['street'];
            street.appendChild(option);
          });
          if(street_set != "") {
            street.value = street_set;
            street_set = "";
          }
        }).catch(function(error) {
          // console.log(error);
        });
    }
    const city = document.getElementById('city');
    const city2 = document.getElementById('city2');
    city.addEventListener('change', function() {
        setStreet('prefecture', 'city', 'street');
    });
    city2.addEventListener('change', function() {
        setStreet('prefecture2', 'city2', 'street2');
    });
  </script>
</div>
</x-layouts.sample>