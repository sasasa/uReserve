<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'number_of_people',
    ];

    protected $casts = [
        'canceled_date' => 'datetime:Y-m-d H:i:00'
    ];

    public function scopeLatestMine($query, int $eventId)
    {
        return $query->where('user_id', '=', Auth::id())
        ->where('event_id', '=', $eventId)
        ->latest(); // 引数なしだとcreated_atが新しい順
    }

    public function scopeNoneCanceleNumberOfPeople($query, int $eventId)
    {
        return $query->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
            ->whereNull('canceled_date')
            ->where('event_id', $eventId)
            ->groupBy('event_id');
    }
}
