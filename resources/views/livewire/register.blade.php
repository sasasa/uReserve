<div class="container mx-auto">
    @if (session()->has('message'))
    <div class="">
        {{ session('message') }}
    </div>
    @endif
    <div>
        <label for="search">検索</label>
        <input id="search" type="text" wire:model.debounce.400ms="search">
    </div>
    <button wire:click="create">+新規作成する</button>
    
    <form wire:submit.prevent="register" novalidate>
        <div>
            {{-- dummy autocomp対策 --}}
            <input type="email" name="email" style="visibility: hidden;" >
            <input type="password" name="password" style="visibility: hidden;" >
        </div>

        <label>名前
            <input type="text" wire:model.debounce.400ms="user.name" >
        </label><br>
        @error('user.name') <div class="text-red-500">{{ $message }}</div> @enderror

        <label>メールアドレス
            <input type="text" inputmode="email" wire:model.debounce.400ms="user.email">
        </label><br>
        @error('user.email') <div class="text-red-500">{{ $message }}</div> @enderror

        <label>パスワード
            <input type="password" wire:model.debounce.400ms="user.password" >
        </label><br>
        @error('user.password') <div class="text-red-500">{{ $message }}</div> @enderror

        @for($i = 1; $i < $cnt; $i++)
        <p><label>自由入力欄<input type="text" wire:model.debounce.400ms="text_{{ $i }}" value=""></label></p>
        @error("text_{$i}") <div class="text-red-500">{{ $message }}</div> @enderror
        @endfor
        <p><button wire:click="add">add</button></p>
        <p><button wire:click="del">del</button></p>

        @if($user->exists)
            <button type="submit" class="bg-blue-700 text-blue-50 p-2 rounded">変更する</button>
        @else
            <button type="submit" class="bg-purple-700 text-purple-50 p-2 rounded">登録する</button>
        @endif
    </form>
    <table class="w-full text-sm mb-5">
        <thead>
        <tr>
            <th class="border p-2">名前</th>
            <th class="border p-2">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td class="border px-2 py-1">{{ $user->name }}</td>
                <td class="border px-2 py-1 text-right">
                    <button
                        type="button"
                        class="bg-yellow-500 text-yellow-50 rounded p-2 text-xs"
                        wire:click="$emit('edit', {{ $user->id }})">
                        変更
                    </button>
                    <button
                        type="button"
                        class="bg-red-600 text-red-50 rounded p-2 text-xs"
                        onClick="onDelete({{ $user->id }})">
                        削除
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
        {{ $users->links() }}
    </table>

    <script>
        function onDelete(id) {
            if(confirm('削除します。よろしいですか？')) {
                Livewire.emit('destroy', id);
            }
        }
    </script>
</div>
