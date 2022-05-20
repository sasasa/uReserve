<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'number_of_people',
    ];

    public function scopeLatestMine($query, $eventId)
    {
        return $query->where('user_id', '=', Auth::id())
        ->where('event_id', '=', $eventId)
        ->latest(); // 引数なしだとcreated_atが新しい順
    }
}
