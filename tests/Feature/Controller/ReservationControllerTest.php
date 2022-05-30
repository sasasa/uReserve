<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function dashboard_ログインせずアクセスるとログインページにリダイレクト()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function dashboard_正常に表示されること()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function reserve_予約できること()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        // 未来
        $event1 = Event::factory()->create([
            'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->post(route('events.reserve', ['event' => $event1]), [
            'reserved_people' => 8,
        ]);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('status', '登録OKです');
        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'number_of_people' => 8,
        ]);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function detail_正常に表示されること()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $expectEvent = [
            'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => 1,
            'max_people' => 12,
        ];
        $event1 = Event::factory()->create($expectEvent);
        $expectReservation = [
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'number_of_people' => 5,
            'canceled_date' => null,
            'created_at' => now(),
        ];
        DB::table('reservations')->insert([
            [
                'user_id' => $user->id,
                'event_id' => $event1->id,
                'number_of_people' => 5,
                'canceled_date' => now()->subDays(3),
                'created_at' => now()->subDays(3),
            ],
            [
                'user_id' => $user->id,
                'event_id' => $event1->id,
                'number_of_people' => 5,
                'canceled_date' => now()->subDays(2),
                'created_at' => now()->subDays(2),
            ],
            $expectReservation,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('events.detail', ['event' => $event1]));
        $response->assertStatus(200);

        $response->assertViewHas('event');
        $event = $response->original['event'];
        // イベント
        $this->assertEquals($expectEvent['start_date'], $event->toArray()['start_date']);
        $this->assertEquals($expectEvent['end_date'], $event->toArray()['end_date']);
        $this->assertEquals($expectEvent['max_people'], $event->toArray()['max_people']);

        $response->assertViewHas('resevablePeople');
        $resevablePeople = $response->original['resevablePeople'];
        // 予約可能な人数
        $this->assertEquals(12 - 5, $resevablePeople);

        $response->assertViewHas('isReserved');
        $isReserved = $response->original['isReserved'];
        // 一番最新の予約
        $this->assertEquals($expectReservation['user_id'], $isReserved->toArray()['user_id']);
        $this->assertEquals($expectReservation['event_id'], $isReserved->toArray()['event_id']);
        $this->assertEquals($expectReservation['number_of_people'], $isReserved->toArray()['number_of_people']);
    }
}
