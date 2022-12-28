<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $title ?? "livewire sample" }}</title>
  {{-- <link rel="stylesheet" href="{{ mix('css/app.css') }}"> --}}
  @livewireStyles
</head>
<body>
  <h1>{{ $hige ?? '---' }}</h1>
  <h1>livewire sample</h1>
  {{ $slot }}
  @livewireScripts
  <script defer src="{{ mix('js/app.js') }}"></script>
</body>
</html>