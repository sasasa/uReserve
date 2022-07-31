<?php
declare(strict_types=1);
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Reservation;

class ReservationService
{
    /**
     * @param Event $event
     * @return int
     */
    public function getResevablePeople(Event $event): int
    {
        $reservedPeople = Reservation::noneCanceleNumberOfPeople($event->id)->first();

        if($reservedPeople) { 
            $resevablePeople = $event->max_people - $reservedPeople->number_of_people;
        } else { 
            $resevablePeople = $event->max_people;
        }
        return $resevablePeople;
    }
    /**
     * @param Event $event
     * @param int $add_number_people
     * @return bool
     */
    public function canReserve(Event $event, int $add_number_people): bool
    {
        $reservedPeople = Reservation::noneCanceleNumberOfPeople($event->id)->first();

        return (is_null($reservedPeople) || $event->max_people >= $reservedPeople->number_of_people + $add_number_people);
    }
    
}