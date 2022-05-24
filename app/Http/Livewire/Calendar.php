<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Services\EventService;

class Calendar extends Component
{
    public $currentDate;
    public $currentWeek;
    public $events;

    public function getDate($date, EventService $eventService)
    {
        $this->currentDate = CarbonImmutable::parse($date)->format('Y-m-d');
        $this->events = $eventService->getWeekEvents(
            $this->currentDate,
            CarbonImmutable::parse($date)->addDays(7)->format('Y-m-d')
        );

        $this->currentWeek = [];
        for($i = 0; $i < 7; $i++ )
        {
            array_push($this->currentWeek, [
                // カレンダー表示用 (○月△日)
                'day' => CarbonImmutable::parse($this->currentDate)->addDays($i)->format('m月d日'), 
                // 判定用 (○○○○-△△-□□)
                'checkDay' => CarbonImmutable::parse($this->currentDate)->addDays($i)->format('Y-m-d'),
                // 曜日
                'dayOfWeek' => CarbonImmutable::parse($this->currentDate)->addDays($i)->dayName,
            ]);
        }
    }

    public function mount(EventService $eventService)
    {
        $this->currentDate = CarbonImmutable::today()->format('Y-m-d');
        $this->events = $eventService->getWeekEvents(
            $this->currentDate,
            CarbonImmutable::today()->addDays(7)->format('Y-m-d')
        );

        $this->currentWeek = [];
        for($i = 0; $i < 7; $i++ ) {
            array_push($this->currentWeek, [
                // カレンダー表示用 (○月△日)
                'day' => CarbonImmutable::parse($this->currentDate)->addDays($i)->format('m月d日'),
                // 判定用 (○○○○-△△-□□)
                'checkDay' => CarbonImmutable::parse($this->currentDate)->addDays($i)->format('Y-m-d'),
                // 曜日
                'dayOfWeek' => CarbonImmutable::parse($this->currentDate)->addDays($i)->dayName,
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.calendar');
    }
}
