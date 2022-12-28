<x-layouts.sample title="確認画面">
  <div>
    <div id="target"></div>
    <form method="POST">
        @csrf
        {{ session('lat') }}
        {{ session('lng') }}
        {{ session('postalCode') }}
        {{ session('prefecture') }}
        {{ session('city') }}
        {{ session('street') }}
        {{ session('block') }}
        <div>
            <button type="button" onclick="location.href='/sample'">戻る</button>
            <button type="submit">Save</button>
        </div>
    </form>
<style>
#target {
    width: 550px;
    height: 200px;
}
</style>
<script async defer src="https://maps.googleapis.com/maps/api/js?language=ja&region=JP&key={{ env('GOOGLE_MAP_API_KEY') }}&callback=initMap"></script>
<script>
    function initMap() {
      const target = document.getElementById('target');
      let map;
      let center = { lat: {{ session('lat') }}, lng: {{ session('lng') }} };
      let marker;

      map = new google.maps.Map(target, {
        center: center,
        zoom: 18,
        disableDefaultUI: true,
        clickableIcons: false,
      });
      marker = new google.maps.Marker({
          position: center,
          map: map,
          animation: google.maps.Animation.DROP,
      });
    }
</script>
</div>
</x-layouts.sample>