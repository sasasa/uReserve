<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\ReservationService;
use App\Http\Requests\ReservationRequest;

class ReservationController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function detail(Event $event, ReservationService $reservationService)
    {
        $resevablePeople = $reservationService->getResevablePeople($event);
        $isReserved = Reservation::latestMine($event->id)->whereNull('canceled_date')->first();
        
        return view('event-detail', compact('event', 'resevablePeople', 'isReserved'));
    } 

    public function reserve(ReservationRequest $request, Event $event)
    {
        Reservation::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'number_of_people' => $request->reserved_people,
        ]);
        session()->flash('status', '登録OKです');
        return to_route('dashboard');
    }
}
