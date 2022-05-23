<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ReservationService;
use App\Models\Event;
use App\Models\User;
use App\Models\Reservation;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private ReservationService $service;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ReservationService();
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function getResevablePeople_予約が入っていない場合()
    {
        $event = Event::factory()->create([
            'max_people' => 15,
        ]);
        $resevablePeople = $this->service->getResevablePeople($event);
        self::assertSame(15, $resevablePeople);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function getResevablePeople_予約が入っている場合()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'max_people' => 15,
        ]);
        Reservation::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'number_of_people' => 5,
        ]);
        $resevablePeople = $this->service->getResevablePeople($event);
        self::assertSame(10, $resevablePeople);
    }

    /**
     * A basic feature test example.
     * @test
     * @dataProvider canReserveDataProvider
     * @return void
     */
    public function canReserve_予約がある場合($expect, $reserved_people)
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'max_people' => 15,
        ]);
        Reservation::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'number_of_people' => 5,
        ]);
        $canReserve = $this->service->canReserve($event, $reserved_people);
        self::assertSame($expect, $canReserve);
    }

    /**
     * データプロバイダ
     * @see https://phpunit.readthedocs.io/ja/latest/writing-tests-for-phpunit.html#writing-tests-for-phpunit-data-providers
     *
     * @return array
     */
    public function canReserveDataProvider()
    {
        return [
            // $expect, $reserved_people
            '1人なら予約できる' => [true, 1],
            '10人なら予約できる' => [true, 10],
            '11人なら予約できない' => [false, 11],
        ];
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function canReserve_予約が無い場合()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'max_people' => 15,
        ]);
        $canReserve = $this->service->canReserve($event, 15);
        self::assertSame(true, $canReserve);
    }
}
