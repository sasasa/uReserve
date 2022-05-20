<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          イベント詳細
      </h2>
  </x-slot>

  <div class="pt-4 pb-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="max-w-2xl mx-auto py-4">
              <x-jet-validation-errors class="mb-4" />
  
              @if (session('status'))
                  <div class="mb-4 font-medium text-sm text-green-600">
                      {{ session('status') }}
                  </div>
              @endif
      
              <form method="post" action="{{ route('events.reserve', ['event' => $event]) }}"> 
                  @csrf
                  <div class="mt-4">
                      <x-jet-label for="event_name" value="イベント名" />
                      {{ $event->name }}
                  </div>
              
                  <div class="mt-4">
                      <x-jet-label for="information" value="イベント詳細" />
                      {!! nl2br(e($event->information)) !!}
                  </div>
                  
                  <div class="md:flex justify-between">
                    <div class="mt-4">
                        <x-jet-label for="event_date" value="イベント日付" />
                        {{ $event->eventDate }}
                    </div>
                    <div class="mt-4">
                        <x-jet-label for="start_time" value="開始時間" />
                        {{ $event->startTime }}
                    </div>
                    <div class="mt-4">
                        <x-jet-label for="end_time" value="終了時間" />
                        {{ $event->endTime }}
                    </div>
                  </div>

                  <div class="md:flex justify-between items-end">
                    <div class="mt-4">
                      <x-jet-label for="max_people" value="定員数" />
                      {{ $event->max_people }}
                    </div>

                    <div class="mt-4">
                      @if($resevablePeople <= 0)
                        <span class="text-red-500 text-xs">このイベントは満員です。</span>
                      @else
                        <x-jet-label for="reserved_people" value="予約人数" />
                        <select name="reserved_people">
                          @for($i = 1; $i <= $resevablePeople; $i++)
                            <option value="{{$i}}">{{$i}}</option>
                          @endfor
                        </select>
                      @endif
                    </div>

                    @if($resevablePeople > 0 && !$isReserved)
                      <x-jet-button class="ml-4">
                        予約する
                      </x-jet-button>
                    @elseif($isReserved)
                      <span class="text-xs">このイベントは既に予約済みです。</span>
                    @elseif($resevablePeople <= 0)
                      <span class="text-xs">このイベントは既に満員です。</span>
                    @endif
                  </div>
              </form>
            </div>
          </div>
      </div>
  </div>
</x-app-layout>
