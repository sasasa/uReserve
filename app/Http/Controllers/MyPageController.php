<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Services\MyPageService;
use Carbon\Carbon; 

class MyPageController extends Controller
{
    public function __construct(private MyPageService $myPageService)
    {
    }
    
    public function index(){
        $events = Auth::user()->events;
        $fromTodayEvents = $this->myPageService->reservedEvent($events, 'fromToday');
        $pastEvents = $this->myPageService->reservedEvent($events, 'past');

        return view('mypage/index', compact('fromTodayEvents', 'pastEvents'));
    }

    public function show(Event $event)
    {
        $reservation = Reservation::latestMine($event->id)->first();

        return view('mypage/show', compact('event', 'reservation'));
    }

    public function cancel(Event $event)
    {
        $reservation = Reservation::latestMine($event->id)->first();
        $reservation->canceled_date = Carbon::now()->format('Y-m-d H:i:s');
        $reservation->save();
        // session()->flash('status', 'キャンセルできました。');
        return to_route('dashboard')->with('status', 'キャンセルできました。');
    }
}
