<div style="text-align: center">
    <button wire:click="increment">+</button>
    {{-- // wire:click=“メソッド名”で実行 --}}
    <h1>{{ $count }}</h1>
    {{-- // Counterクラス内プロパティを表示 --}}


    <input wire:model.debounce.2000ms="name" type=“text”>
    {{-- <input wire:model.lazy="name" type=“text”> --}}
    {{-- <input wire:model.defer="name" type=“text”> --}}
    <h2>こんにちは {{ $name }} さん</h2>

    <button wire:mouseover="mouseOver">マウスを合わせてね</button>
</div>