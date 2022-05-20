<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function detail(Event $event)
    {
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->where('event_id', $event->id)
        ->groupBy('event_id')
        // ->having('event_id', $event->id ) // havingはgroupByの後に検索
        ->first();
        if(!is_null($reservedPeople)) { 
            $resevablePeople = $event->max_people - $reservedPeople->number_of_people;
        } else { 
            $resevablePeople = $event->max_people;
        }
        $isReserved = Reservation::latestMine($event->id)->whereNull('canceled_date')->first();
        
        return view('event-detail', compact('event', 'resevablePeople', 'isReserved'));
    } 

    public function reserve(Request $request, Event $event)
    {
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->where('event_id', $event->id)
        ->groupBy('event_id')
        ->first();

        if(is_null($reservedPeople) || $event->max_people >= $reservedPeople->number_of_people + $request->reserved_people) {
            Reservation::create([
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'number_of_people' => $request->reserved_people,
            ]);
            session()->flash('status', '登録OKです');
            return to_route('dashboard');
        } else {
            session()->flash('status', 'この人数は予約できません。');
            return view('dashboard');
        }
    }
}
