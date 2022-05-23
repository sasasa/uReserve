<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\MyPageService;
use App\Models\Event;
use App\Models\User;
use App\Models\Reservation;
use InvalidArgumentException;

class MyPageServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private MyPageService $service;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MyPageService();
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function reservedEvent_過去()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'start_date' => now()->subDays(3),
            'end_date' => now()->subDays(3)->addHours(1),
        ]);
        Reservation::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'number_of_people' => 5,
        ]);
        $events = $this->service->reservedEvent($user->events, 'past');
        $this->assertCount(1, $events);
    }
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function reservedEvent_現在未来()
    {
        $user = User::factory()->create();
        $event1 = Event::factory()->create([
            'start_date' => now(),
            'end_date' => now()->addHours(1),
        ]);
        $event2 = Event::factory()->create([
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(3)->addHours(1),
        ]);
        Reservation::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'number_of_people' => 5,
        ]);
        Reservation::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event2->id,
            'number_of_people' => 5,
        ]);
        $events = $this->service->reservedEvent($user->events, 'fromToday');
        $this->assertCount(2, $events);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function reservedEvent_例外発生()
    {
        $user = User::factory()->create();
        $event1 = Event::factory()->create([
            'start_date' => now(),
            'end_date' => now()->addHours(1),
        ]);
        $event2 = Event::factory()->create([
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(3)->addHours(1),
        ]);
        Reservation::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'number_of_people' => 5,
        ]);
        Reservation::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event2->id,
            'number_of_people' => 5,
        ]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('引数はfromTodayかpastにしてください');
        $events = $this->service->reservedEvent($user->events, 'nothingValue');
    }
}
