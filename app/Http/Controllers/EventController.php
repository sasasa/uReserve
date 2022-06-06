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
        $events = Event::withNumberOfPeople()->future()->orderBy('start_date', 'asc')->paginate(10);
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
        $eventService->saveJoinDateAndTime($request->all());

        // session()->flash("status", "登録okです");
        return to_route("events.index")->with("status", "登録okです");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        $reservations = $event->reservations();
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
        $eventService->saveJoinDateAndTime($request->all(), $event);

        // session()->flash("status", "更新しました。");
        return to_route("events.index")->with("status", "更新しました。");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Event $event)
    // {
    //     //
    // }

    public function past()
    {
        $events = Event::withNumberOfPeople()->past()->orderBy('start_date', 'desc')->paginate(10);
        return view('manager.events.past', compact('events')); 
    }
    
}
