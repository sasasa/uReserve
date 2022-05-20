<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  @livewireStyles
  <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body>
  livewireテスト
  {{-- <livewire:register /> --}}
  @livewire("register")

  @livewireScripts
</body>
</html>