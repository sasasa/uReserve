<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Reservation;

class EventService
{
    public function getWeekEvents($startDate, $endDate)
    {
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNotNull('canceled_date')
        ->groupBy('event_id');

        return DB::table('events')
        ->leftJoinSub($reservedPeople, 'reservedPeople', function($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
            })
        ->whereBetween('start_date', [$startDate, $endDate])
        ->orderBy('start_date', 'asc')
        ->get();
    }

    public function checkEditEventDuplication(int $id, $eventDate, $startTime, $endTime)
    {
        return  DB::table('events')
            ->where('id', '<>', $id)
            ->whereDate('start_date', $eventDate)
            ->whereTime('end_date' ,'>',$startTime)
            ->whereTime('start_date', '<', $endTime)
            ->exists();
    }

    public function checkEventDuplication($eventDate, $startTime, $endTime)
    {
        return DB::table('events')
            ->whereDate('start_date', $eventDate)
            ->whereTime('end_date' , '>', $startTime)
            ->whereTime('start_date', '<', $endTime)
            ->exists();
    }

    public function joinDateAndTime($date, $time)
    {
        $join = $date . " " . $time;
        return Carbon::createFromFormat('Y-m-d H:i', $join);
    }
}