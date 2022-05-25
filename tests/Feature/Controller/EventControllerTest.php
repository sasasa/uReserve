<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function edit_表示されること()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        // 未来
        $event = Event::factory()->create([
            'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('events.edit', ['event' => $event]));
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function create_表示されること()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('events.create'));
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function past_過去のeventのみ表示されること()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        // 未来
        Event::factory()->create([
            'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        // 過去
        Event::factory()->create([
            'start_date' => Carbon::now()->subDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->subDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        Event::factory()->create([
            'start_date' => Carbon::now()->subDays(2)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->subDays(2)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        Event::factory()->create([
            'start_date' => Carbon::now()->subDays(1)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->subDays(1)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('events.past'));
        $response->assertStatus(200);
        $response->assertViewHas('events');
        $events = $response->original['events'];
        // 過去のイベントのみ
        $this->assertEquals(3, count($events));
    }
}
