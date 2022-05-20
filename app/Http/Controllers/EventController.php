<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 
use App\Services\EventService;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservedPeople = Reservation::select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
            ->whereNull('canceled_date')
            ->groupBy('event_id');
        
        $events = Event::leftJoinSub($reservedPeople, 'reservedPeople', function($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
        })
        ->whereDate('start_date', '>=' , Carbon::today())
        ->orderBy('start_date', 'asc') //開始日時順
        ->paginate(10); // 10件ずつ
        // ->get();
        // dd($events);
        return view("manager.events.index", compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("manager.events.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request, EventService $eventService)
    {
        $check = $eventService->checkEventDuplication(
            $request['event_date'], $request['start_time'], $request['end_time']
        );
        if($check){
            // 存在したら
            session()->flash('status', 'この時間帯は既に他の予約が存在します。');
            return view('manager.events.create');
        }
        $startDate = $eventService->joinDateAndTime($request['event_date'], $request['start_time']);
        $endDate = $eventService->joinDateAndTime($request['event_date'], $request['end_time']); 
        Event::create([
            'name' => $request['event_name'],
            'information' => $request['information'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'max_people' => $request['max_people'],
            'is_visible' => $request['is_visible'],
        ]);
        session()->flash("status", "登録okです");
        return to_route("events.index");//名前付きルート
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        $reservations = []; // 連想配列を作成
        foreach($event->users as $user)
        {
            $reservedInfo = [
                'name' => $user->name,
                'number_of_people' => $user->pivot->number_of_people,
                'canceled_date' => $user->pivot->canceled_date
            ];
            array_push($reservations, $reservedInfo); // 連想配列に追加
        }
        // dd($reservations);
        return view("manager.events.show", compact("event", 'reservations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        return view("manager.events.edit", compact("event"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventRequest  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventRequest $request, Event $event, EventService $eventService)
    {
        $check = $eventService->checkEditEventDuplication(
            $event->id, $request['event_date'], $request['start_time'], $request['end_time']
        );
        if($check){
            // 存在したら
            session()->flash('status', 'この時間帯は既に他の予約が存在します。');
            return view('manager.events.edit', compact('event'));
        }
        $startDate = $eventService->joinDateAndTime($request['event_date'], $request['start_time']);
        $endDate = $eventService->joinDateAndTime($request['event_date'], $request['end_time']); 
        $event->fill([
            'name' => $request['event_name'],
            'information' => $request['information'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'max_people' => $request['max_people'],
            'is_visible' => $request['is_visible'],
        ]);
        $event->save();
        session()->flash("status", "更新しました。");
        return to_route("events.index");//名前付きルート
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }

    public function past()
    {
        $reservedPeople = Reservation::select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
            ->whereNull('canceled_date')
            ->groupBy('event_id');

        $events = Event::leftJoinSub($reservedPeople, 'reservedPeople', function($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
        })
        ->whereDate('start_date', '<', Carbon::today())
        ->orderBy('start_date', 'desc')
        ->paginate(10);
        return view('manager.events.past', compact('events')); 
    }
    
}
