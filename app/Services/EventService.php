<?php
declare(strict_types=1);
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Reservation;
use Exception;

class EventService
{
    /**
     * @param array{
     *   event_date: string,
     *   start_time: string,
     *   end_time: string,
     *   event_name: string,
     *   information: string,
     *   max_people: int,
     *   is_visible: int,
     * } $request
     * @param Event $model
     */
    public function saveJoinDateAndTime(array $request, Event $model = new Event())
    {
        $startDate = $this->joinDateAndTime($request['event_date'], $request['start_time']);
        $endDate = $this->joinDateAndTime($request['event_date'], $request['end_time']);
        $model->fill([
            'name' => $request['event_name'],
            'information' => $request['information'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'max_people' => $request['max_people'],
            'is_visible' => $request['is_visible'],
        ]);
        return $model->save() ? $model : (throw new \Exception('db error'));
    }
    
    /**
     * @param string $startDate
     * @param string $endDate
     * @return Illuminate\Database\Eloquent\Collection<Event>
     */
    public function getWeekEvents($startDate, $endDate)
    {
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id');

        return Event::leftJoinSub($reservedPeople, 'reservedPeople', function($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
            })
        ->whereBetween('start_date', [$startDate, $endDate])
        ->where('is_visible', true)
        ->orderBy('start_date', 'asc')
        ->get();
    }

    /**
     * @return bool
     */
    public function checkEditEventDuplication(int $id, $eventDate, $startTime, $endTime)
    {
        return  DB::table('events')
            ->where('id', '<>', $id)
            ->whereDate('start_date', $eventDate)
            ->whereTime('end_date' ,'>',$startTime)
            ->whereTime('start_date', '<', $endTime)
            ->exists();
    }

    /**
     * @return bool
     */
    public function checkEventDuplication($eventDate, $startTime, $endTime)
    {
        return DB::table('events')
            ->whereDate('start_date', $eventDate)
            ->whereTime('end_date' , '>', $startTime)
            ->whereTime('start_date', '<', $endTime)
            ->exists();
    }

    /**
     * @param string $date
     * @param string $time
     * @return Carbon
     */
    public function joinDateAndTime($date, $time)
    {
        $join = $date . " " . $time;
        return Carbon::createFromFormat('Y-m-d H:i', $join);
    }
}