<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EventService;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private EventService $service;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new EventService();
    }
    
    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function saveJoinDateAndTime_正常新規作成()
    {
        $this->service->saveJoinDateAndTime([
            'event_date' => '2022-03-12',
            'start_time' => '12:00',
            'end_time' => '13:00',
            'event_name' => 'イベント名',
            'information' => 'イベント情報',
            'max_people' => 15,
            'is_visible' => 1,
        ]);
        $this->assertDatabaseHas('events', [
            'start_date' => '2022-03-12 12:00',
            'end_date' => '2022-03-12 13:00',
            'name' => 'イベント名',
            'information' => 'イベント情報',
            'max_people' => 15,
            'is_visible' => 1,
        ]);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function saveJoinDateAndTime_正常更新()
    {
        $event = Event::factory()->create();
        $this->service->saveJoinDateAndTime([
            'event_date' => '2022-03-12',
            'start_time' => '12:00',
            'end_time' => '13:00',
            'event_name' => 'イベント名',
            'information' => 'イベント情報',
            'max_people' => 15,
            'is_visible' => 1,
        ], $event);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'start_date' => '2022-03-12 12:00',
            'end_date' => '2022-03-12 13:00',
            'name' => 'イベント名',
            'information' => 'イベント情報',
            'max_people' => 15,
            'is_visible' => 1,
        ]);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function getWeekEvents_正常取得()
    {
        Event::factory()->create([
            'start_date' => '2022-03-12 12:00:00',
            'end_date' => '2022-03-12 13:00:00',
            'is_visible' => true,
        ]);
        Event::factory()->create([
            'start_date' => '2022-03-13 12:00:00',
            'end_date' => '2022-03-13 13:00:00',
            'is_visible' => true,
        ]);
        Event::factory()->create([
            'start_date' => '2022-03-14 12:00:00',
            'end_date' => '2022-03-14 13:00:00',
            'is_visible' => true,
        ]);
        // これは含まれない
        Event::factory()->create([
            'start_date' => '2022-03-15 12:00:00',
            'end_date' => '2022-03-15 13:00:00',
            'is_visible' => true,
        ]);
        // これは含まれない
        Event::factory()->create([
            'start_date' => '2022-03-16 12:00:00',
            'end_date' => '2022-03-16 13:00:00',
            'is_visible' => true,
        ]);
        $events = $this->service->getWeekEvents('2022-03-12', '2022-03-15');
        self::assertCount(3, $events);
    }


    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function joinDateAndTime_正常()
    {
        $carbon = $this->service->joinDateAndTime('2022-01-14', '12:30');
        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame('2022-01-14 12:30:00', $carbon->format('Y-m-d H:i:s'));
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function checkEventDuplication_重複あり()
    {
        Event::factory()->create([
            'start_date' => '2022-03-16 12:00:00',
            'end_date' => '2022-03-16 13:00:00',
            'is_visible' => true,
        ]);
        $isDuplicate = $this->service->checkEventDuplication('2022-03-16', '12:00', '13:00');
        $this->assertSame(true, $isDuplicate);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function checkEventDuplication_重複なし()
    {
        Event::factory()->create([
            'start_date' => '2022-03-16 12:00:00',
            'end_date' => '2022-03-16 13:00:00',
            'is_visible' => true,
        ]);
        $isDuplicate = $this->service->checkEventDuplication('2022-03-16', '13:00', '14:00');
        $this->assertSame(false, $isDuplicate);
    }

    
    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function checkEditEventDuplication_自分自身とは重複なし()
    {
        $event = Event::factory()->create([
            'start_date' => '2022-03-16 12:00:00',
            'end_date' => '2022-03-16 13:00:00',
            'is_visible' => true,
        ]);
        $isDuplicate = $this->service->checkEditEventDuplication($event->id, '2022-03-16', '12:00', '13:00');
        $this->assertSame(false, $isDuplicate);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function checkEditEventDuplication_他人とは重複する()
    {
        $event = Event::factory()->create([
            'start_date' => '2022-03-16 12:00:00',
            'end_date' => '2022-03-16 13:00:00',
            'is_visible' => true,
        ]);
        Event::factory()->create([
            'start_date' => '2022-03-16 12:00:00',
            'end_date' => '2022-03-16 13:00:00',
            'is_visible' => true,
        ]);
        $isDuplicate = $this->service->checkEditEventDuplication($event->id, '2022-03-16', '12:00', '13:00');
        $this->assertSame(true, $isDuplicate);
    }
}
