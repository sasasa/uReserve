<div>
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input
        id="calendar"
        class="block mt-1 mb-2 mx-auto"
        type="text"
        name="calendar"
        value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)"
        readonly
    />
    <div class="flex mx-auto">
        <x-calendar-time />
        @for ($i = 0; $i < 7; $i++)
            <div class="w-32">
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['day'] }}</div>
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
                @for($j = 0; $j < 21; $j++)
                    @if($events->isNotEmpty())
                        @php
                            $eventInfo = $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . " " . \Constant::EVENT_TIME[$j] );
                            if($eventInfo) {
                                $eventName = $eventInfo->name;
                                $number_of_people = $eventInfo->number_of_people;
                                $max_people = $eventInfo->max_people;
                                $eventPeriod = \Carbon\Carbon::parse($eventInfo->start_date)->diffInMinutes($eventInfo->end_date) / 30 - 1;
                            }
                        @endphp
                        @if(!is_null($eventInfo))
                            <a href="{{ route('events.detail', ['event' => $eventInfo->id]) }}">
                                <div class="py-1 px-2 h-8 border border-gray-200 text-xs bg-blue-100">
                                    {{ $eventName }}&nbsp;{{ ($number_of_people ?? 0).'/'.$max_people }}
                                </div>
                            </a>
                            @if( $eventPeriod > 0 )
                                @for($k = 0; $k < $eventPeriod ; $k++)
                                    <div class="py-1 px-2 h-8 border border-gray-200 bg-blue-100"></div>
                                @endfor
                                @php $j += $eventPeriod @endphp
                            @endif
                        @else
                            <div class="py-1 px-2 h-8 border border-gray-200"></div>
                        @endif
                    @else
                        <div class="py-1 px-2 h-8 border border-gray-200"></div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>
</div>