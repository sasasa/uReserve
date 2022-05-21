<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          イベント編集
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="max-w-2xl mx-auto py-4">
              <x-jet-validation-errors class="mb-4" />
  
              @if (session('status'))
                  <div class="mb-4 font-medium text-sm text-green-600">
                      {{ session('status') }}
                  </div>
              @endif
      
              <form method="POST" action="{{ route('events.update', ['event' => $event]) }}">
                  @csrf
                  @method('PATCH')
                  <div class="mt-4">
                      <x-jet-label for="event_name" value="イベント名" />
                      <x-jet-input id="event_name" class="block mt-1 w-full" type="text" name="event_name" :value="old('event_name', $event->name)" required autofocus />
                  </div>
              
                  <div class="mt-4">
                      <x-jet-label for="information" value="イベント詳細" />
                      <x-textarea row="3" name="information" id="information" class="block mt-1 w-full" >{{ old('information', $event->information) }}</x-textarea>
                  </div>
                  
                  <div class="md:flex justify-between">
                    <div class="mt-4">
                        <x-jet-label for="event_date" value="イベント日付" />
                        <x-jet-input id="event_date" class="block mt-1 w-full" type="text" name="event_date" required :value="old('event_date', $event->editEventDate)" />
                    </div>
                    <div class="mt-4">
                        <x-jet-label for="start_time" value="開始時間" />
                        <x-jet-input id="start_time" class="block mt-1 w-full" type="text" name="start_time" required :value="old('start_time', $event->editStartTime)" />
                    </div>
                    <div class="mt-4">
                        <x-jet-label for="end_time" value="終了時間" />
                        <x-jet-input id="end_time" class="block mt-1 w-full" type="text" name="end_time" required :value="old('end_time', $event->editEndTime)"/>
                    </div>
                  </div>

                  <div class="md:flex justify-between items-end">
                    <div class="mt-4">
                      <x-jet-label for="max_people" value="定員数" />
                      <x-jet-input id="max_people" class="block mt-1 w-full" type="number" name="max_people" required :value="old('max_people', $event->max_people)" />
                    </div>
                    <div class="flex space-x-4 justify-around">
                      <label class="flex items-center">
                        <input class="mr-2" type="radio" name="is_visible" value="1" @if(old('is_visible', $event->is_visible) == "1") checked @endif />表示
                      </label>
                      <label class="flex items-center">
                        <input class="mr-2" type="radio" name="is_visible" value="0" @if(old('is_visible', $event->is_visible) == "0") checked @endif />非表示
                      </label>
                    </div>
                    <x-jet-button class="ml-4">
                      更新する
                    </x-jet-button>
                  </div>
              </form>
            </div>
          </div>
      </div>
  </div>
  <script src="{{ mix("js/flatpickr.js")}}">
  </script>
</x-app-layout>
