<x-layouts.sample title="確認画面">
<div>
  〒<input type="text" name="postal" id="postal">
  <ul id="address_list">
  </ul>
  <br>

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

  <script>
    let city_set = "";
    let street_set = "";

    function setAddress(prefecture, city, street)
    {
        const pref = document.getElementById('prefecture');  
        pref.value = prefecture;
        // prefectureのeventを発生させる
        city_set = city;
        street_set = street;
        pref.dispatchEvent(new Event('change'));
    }

    const postal = document.getElementById('postal');
    postal.addEventListener('change', function() {
      axios.post('{{ route('address') }}', {
          postal: postal.value
      }).then(function(response) {
        const address_list = document.getElementById('address_list');
        address_list.innerHTML = '';
        const prefecture = document.getElementById('prefecture');
        const city = document.getElementById('city');
        const street = document.getElementById('street');
        prefecture.value = '';
        city.value = '';
        street.value = '';

        if(response.data.length == 1) {
          response.data.forEach(function(place) {
            setAddress(place['prefecture'], place['city'], place['street'])
          });
        } else {
          const address_list = document.getElementById('address_list');
          response.data.forEach(function(place) {
            const li = document.createElement('li');
            li.innerHTML = place['prefecture'] + place['city'] + place['street'];
            address_list.appendChild(li);
            li.addEventListener('click', function() {
              setAddress(place['prefecture'], place['city'], place['street'])
            });
          });
        }
      }).catch(function(error) {
        console.log(error);
      });
    });

    const prefecture = document.getElementById('prefecture');
    prefecture.addEventListener('change', function() {
      axios.post('{{ route('cities') }}', {
          prefecture: prefecture.value
      }).then(function(response) {
        console.log(response.data);
        const street = document.getElementById('street');
        street.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.text = '選択してください';
        street.appendChild(opt);

        const city = document.getElementById('city');
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
        console.log(error);
      });
    });

    const city = document.getElementById('city');
    city.addEventListener('change', function() {
      axios.post('{{ route('streets') }}', {
          prefecture: prefecture.value,
          city: city.value
      }).then(function(response) {
        console.log(response.data);
        const street = document.getElementById('street');
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
        console.log(error);
      });
    });
  </script>
</div>
</x-layouts.sample>