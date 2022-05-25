<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MyPageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function index_正常に表示されること()
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
        $event2 = Event::factory()->create([
            'start_date' => Carbon::now()->addDays(2)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->addDays(2)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        // 過去
        $event3 = Event::factory()->create([
            'start_date' => Carbon::now()->subDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->subDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        $event4 = Event::factory()->create([
            'start_date' => Carbon::now()->subDays(2)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->subDays(2)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        $event5 = Event::factory()->create([
            'start_date' => Carbon::now()->subDays(1)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->subDays(1)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        DB::table('reservations')->insert([
            [
                'user_id' => $user->id,
                'event_id' => $event1->id,
                'number_of_people' => 5,
                'canceled_date' => null,
            ],
            [
                'user_id' => $user->id,
                'event_id' => $event2->id,
                'number_of_people' => 3,
                'canceled_date' => null,
            ],
            [
                'user_id' => $user->id,
                'event_id' => $event3->id,
                'number_of_people' => 2,
                'canceled_date' => null,
            ],
            [
                'user_id' => $user->id,
                'event_id' => $event4->id,
                'number_of_people' => 2,
                'canceled_date' => null
            ],
            [
                'user_id' => $user->id,
                'event_id' => $event5->id,
                'number_of_people' => 2,
                'canceled_date' => null,
            ],
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('mypage.index'));
        $response->assertStatus(200);

        $response->assertViewHas('fromTodayEvents');
        $fromTodayEvents = $response->original['fromTodayEvents'];
        // 未来イベントのみ
        $this->assertEquals(2, count($fromTodayEvents));

        $response->assertViewHas('pastEvents');
        $pastEvents = $response->original['pastEvents'];
        // 未来イベントのみ
        $this->assertEquals(3, count($pastEvents));
    }


    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function show_正常に表示されること()
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
            [
                'user_id' => $user->id,
                'event_id' => $event1->id,
                'number_of_people' => 5,
                'canceled_date' => null,
                'created_at' => now(),
            ],
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('mypage.show', ['event' => $event1]));
        $response->assertStatus(200);

        $response->assertViewHas('reservation');
        $reservation = $response->original['reservation'];
        // 一番最新の予約
        $this->assertNull($reservation->canceled_date);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function cancel_予約をキャンセルできること()
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

        $now = now();
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
            [
                'user_id' => $user->id,
                'event_id' => $event1->id,
                'number_of_people' => 5,
                'canceled_date' => null,
                'created_at' => $now,
            ],
        ]);
        $this->actingAs($user, 'web');
        $response = $this->post(route('mypage.cancel', ['event' => $event1]));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('status', 'キャンセルできました。');
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'number_of_people' => 5,
            'canceled_date' => null,
            'created_at' => $now,
        ]);
    }
}
