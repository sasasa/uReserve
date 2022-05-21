<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use App\Services\EventService;
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'information',
        'max_people',
        'start_date',
        'end_date',
        'is_visible',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'reservations')->withPivot('id', 'number_of_people', 'canceled_date');
    }

    public function reservations()
    {
        $reservations = [];
        foreach($this->users as $user)
        {
            array_push($reservations, $user->reservedInfo());
        }
        return collect($reservations);
    }

    public function scopeWithNumberOfPeople($query)
    {
        $reservedPeople = Reservation::select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id');
    
        return $query->leftJoinSub($reservedPeople, 'reservedPeople', function($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
        });
    }

    public function scopePast($query)
    {
        return $query->whereDate('start_date', '<', Carbon::today());
    }

    public function scopeFuture($query)
    {
        return $query->whereDate('start_date', '>=' , Carbon::today());
    }

    protected function editEventDate(): Attribute
    { 
        return new Attribute(
            get: fn () => Carbon::parse($this->start_date)->format('Y-m-d'),
        );
    }
    protected function editStartTime(): Attribute
    { 
        return new Attribute(
            get: fn() => Carbon::parse($this->start_date)->format('H:i')
        );
    }
    protected function editEndTime(): Attribute
    { 
        return new Attribute(
            get: fn() => Carbon::parse($this->end_date)->format('H:i')
        );
    }
    protected function eventDate(): Attribute
    { 
        return new Attribute(
            get: fn() => Carbon::parse($this->start_date)->format('Y年m月d日'),
        );
    } 
    protected function startTime(): Attribute
    { 
        return new Attribute(
            get: fn() => Carbon::parse($this->start_date)->format('H時i分')
        );
    }
    protected function endTime(): Attribute
    { 
        return new Attribute(
            get: fn() => Carbon::parse($this->end_date)->format('H時i分')
        );
    }
}
