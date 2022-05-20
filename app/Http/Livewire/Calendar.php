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
    public $day;
    public $checkDay; // 日付判定用
    public $dayOfWeek; // 曜日
    public $sevenDaysLater;
    public $events;

    public function getDate($date, EventService $eventService)
    {
        $this->currentDate = CarbonImmutable::parse($date); //文字列
        $this->sevenDaysLater = CarbonImmutable::parse($date)->addDays(7);
        $this->events = $eventService->getWeekEvents(
            $this->currentDate->format('Y-m-d'),
            $this->sevenDaysLater->format('Y-m-d')
        );

        $this->currentWeek = [];
        for($i = 0; $i < 7; $i++ )
        {
            $this->day = CarbonImmutable::parse($this->currentDate)->addDays($i)->format('m月d日');
            $this->checkDay = CarbonImmutable::parse($this->currentDate)->addDays($i)->format('Y-m-d');
            $this->dayOfWeek = CarbonImmutable::parse($this->currentDate)->addDays($i)->dayName; 
            // parseでCarbonインスタンスに変換後 日付を加算
            array_push($this->currentWeek, [
                'day' => $this->day, // カレンダー表示用 (○月△日)
                'checkDay' => $this->checkDay, // 判定用 (○○○○-△△-□□)
                'dayOfWeek' => $this->dayOfWeek // 曜日
            ]);
        }
    }

    public function mount(EventService $eventService)
    {
        $this->currentDate = CarbonImmutable::today();
        $this->sevenDaysLater = CarbonImmutable::today()->addDays(7);
        $this->events = $eventService->getWeekEvents(
            $this->currentDate->format('Y-m-d'),
            $this->sevenDaysLater->format('Y-m-d')
        );

        $this->currentWeek = [];
        for($i = 0; $i < 7; $i++ ) {
            $this->day = CarbonImmutable::parse($this->currentDate)->addDays($i)->format('m月d日');
            $this->checkDay = CarbonImmutable::parse($this->currentDate)->addDays($i)->format('Y-m-d');
            $this->dayOfWeek = CarbonImmutable::parse($this->currentDate)->addDays($i)->dayName; 
            // parseでCarbonインスタンスに変換後 日付を加算
            array_push($this->currentWeek, [
                'day' => $this->day, // カレンダー表示用 (○月△日)
                'checkDay' => $this->checkDay, // 判定用 (○○○○-△△-□□)
                'dayOfWeek' => $this->dayOfWeek // 曜日
            ]);
        }
        // dd($this->currentWeek);
    }
    
    public function render()
    {
        return view('livewire.calendar');
    }
}
