<?php

namespace App\Services;
use Carbon\Carbon;
use InvalidArgumentException;
use App\Models\EventInfo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
class MyPageService
{
    public function reservedEvent(Collection $events, string $string): SupportCollection {
        $reservedEvents = collect();
        if($string === 'fromToday') { 
            foreach($events->sortBy('start_date') as $event) {
                if(is_null($event->pivot->canceled_date) && $event->start_date >= Carbon::now()->format('Y-m-d 00:00:00')) {
                    $eventInfo = new EventInfo(
                        $event->id,
                        $event->name,
                        $event->start_date,
                        $event->end_date,
                        $event->pivot->number_of_people
                    );
                    $reservedEvents->push($eventInfo);
                }
            }
        }
        else if($string === 'past'){
            foreach($events->sortByDesc('start_date') as $event) {
                if(is_null($event->pivot->canceled_date) && $event->start_date < Carbon::now()->format('Y-m-d 00:00:00')){
                    $eventInfo = new EventInfo(
                        $event->id,
                        $event->name,
                        $event->start_date,
                        $event->end_date,
                        $event->pivot->number_of_people
                    );
                    $reservedEvents->push($eventInfo);
                }
            }
        } else {
            throw new InvalidArgumentException('引数はfromTodayかpastにしてください');
        }
        return $reservedEvents;
    } 
}
