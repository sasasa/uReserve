<?php

namespace App\Services;
use Carbon\Carbon;
use InvalidArgumentException;
use App\Models\EventInfo;
use Illuminate\Database\Eloquent\Collection;
class MyPageService
{
    public function reservedEvent(Collection $events, string $string) {
        $reservedEvents = [];
        if($string === 'fromToday') { 
            foreach($events->sortBy('start_date') as $event) {
                if(is_null($event->pivot->canceled_date) && $event->start_date >= Carbon::now()->format('Y-m-d 00:00:00')) {
                    $eventInfo = new EventInfo();
                    $eventInfo->id = $event->id;
                    $eventInfo->name = $event->name;
                    $eventInfo->start_date = $event->start_date;
                    $eventInfo->end_date = $event->end_date;
                    $eventInfo->number_of_people = $event->pivot->number_of_people;
                    array_push($reservedEvents, $eventInfo);
                }
            }
        }
        else if($string === 'past'){
            foreach($events->sortByDesc('start_date') as $event) {
                if(is_null($event->pivot->canceled_date) && $event->start_date < Carbon::now()->format('Y-m-d 00:00:00')){
                    $eventInfo = new EventInfo();
                    $eventInfo->id = $event->id;
                    $eventInfo->name = $event->name;
                    $eventInfo->start_date = $event->start_date;
                    $eventInfo->end_date = $event->end_date;
                    $eventInfo->number_of_people = $event->pivot->number_of_people;
                    array_push($reservedEvents, $eventInfo);
                }
            }
        } else {
            throw new InvalidArgumentException('引数はfromTodayかpastにしてください');
        }
        return $reservedEvents;
    } 
}
