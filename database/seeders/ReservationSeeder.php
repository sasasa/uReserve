<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Reservation;
class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // イベント1つにつき1つの予約を入れておく
        Event::get()->each(function($event) {
            $user_id = mt_rand(1, 3);
            $number_of_people = mt_rand(1, $event->max_people);
            DB::table('reservations')->insert([
                [
                    'user_id' => $user_id,
                    'event_id' => $event->id,
                    'number_of_people' => $number_of_people,
                    'canceled_date' => null,
                ],
            ]);
        });
        // DB::table('reservations')->insert([
        //     [
        //         'user_id' => 1,
        //         'event_id' => 1,
        //         'number_of_people' => 5,
        //         'canceled_date' => null,
        //     ],
        //     [
        //         'user_id' => 2,
        //         'event_id' => 1,
        //         'number_of_people' => 3,
        //         'canceled_date' => null,
        //     ],
        //     [
        //         'user_id' => 1,
        //         'event_id' => 2,
        //         'number_of_people' => 2,
        //         'canceled_date' => null,
        //     ],
        //     [
        //         'user_id' => 2,
        //         'event_id' => 2,
        //         'number_of_people' => 2,
        //         'canceled_date' => '2022-03-01 00:00:00'
        //     ],
        //     [
        //         'user_id' => 3,
        //         'event_id' => 3,
        //         'number_of_people' => 2,
        //         'canceled_date' => null,
        //     ],
        // ]);
    }
}
