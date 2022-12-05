<div>
    @if (session()->has('message'))
    <div>
        {{ session('message') }}
    </div>
    @endif
    <form wire:submit.prevent="search">
        <input type="text" wire:model="postalCode">

        <select name="prefecture" id="prefecture" wire:model="prefecture">
            <option value="">選択してください</option>
            @foreach ($prefectures as $prefecture)
            {{-- @selected() --}}
            <option value="{{ $prefecture }}">{{ $prefecture }}</option>
            @endforeach
        </select>

        <select name="city" id="city" wire:model="city">
            <option value="">選択してください</option>
            @foreach ($cities as $place)
            <option value="{{ $place->city }}">{{ $place->city }}</option>
            @endforeach
        </select>
        
        <select name="street" id="street" wire:model="street">
            <option value="">選択してください</option>
            @foreach ($streets as $place)
                @if(!empty($place->street))
                    <option value="{{ $place->street }}">{{ $place->street }}</option>
                @endif
            @endforeach
        </select>

        <input type="text" list="block" wire:model="block">
        <datalist name="block" id="block" wire:model="block">
            <option value="">選択してください</option>
            @foreach ($blocks as $place)
                @if(!empty($place->block))
                    <option value="{{ $place->block }}">{{ $place->block }}</option>
                @endif
            @endforeach
        </datalist>
    </form>
    <form wire:submit.prevent="save">
        @if ($photo)
            <div>
                Photo Preview:
                <img style="object-fit: cover;width: 100px; height: 100px;" src="{{ $photo->temporaryUrl() }}">
            </div>
        @endif

        {{-- <div wire:loading wire:target="photo">Uploading...</div> --}}
        <div>
            <input type="file" wire:model="photo">
        </div>
        @error('photo') <div class="error">{{ $message }}</div> @enderror
        <div>
            <input type="text" wire:model="alt">
        </div>
        @error('alt') <div class="error">{{ $message }}</div> @enderror
        <div>
            <button type="submit" class="border">Save Photo</button>
        </div>
    </form>

    <div>
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
    </div>
</div>
