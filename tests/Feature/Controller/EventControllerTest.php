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
    public function index_権限が無いと表示されないこと()
    {
        $user = User::factory()->create([
            'role' => 9,
        ]);
        // 未来
        $event = Event::factory()->create([
            'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
            'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
            'is_visible' => true,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->get(route('events.index'));
        $response->assertStatus(403);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function index_ログインしていないとログインページにリダイレクトされる()
    {
        $response = $this->get(route('events.index'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

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
     * @dataProvider storeDataProvider
     * @return void
     */
    public function store_登録に成功する場合と失敗する場合($event_name, $information, $event_date, $start_time, $end_time, $max_people, $is_visible, $result, $sessionError)
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $this->actingAs($user, 'web');
        $response = $this->from(route('events.create'))->post(route('events.store'),[
            'event_name' => $event_name,
            'information' => $information,
            'event_date' => $event_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'max_people' => $max_people,
            'is_visible' => $is_visible,
        ]);
        if($result === 'success') {
            $response->assertSessionHas('status', '登録okです');
            $response->assertRedirect(route('events.index'));
            $this->assertDatabaseHas('events', [
                'name' => $event_name,
                'information' => $information,
                'start_date' => $event_date.' '.$start_time,
                'end_date' => $event_date.' '.$end_time,
                'max_people' => $max_people,
                'is_visible' =>  $is_visible,
            ]);
        } 
        if($result === 'error') {
            $response->assertRedirect(route('events.create'));
            $response->assertSessionHasErrors($sessionError);
            $this->assertDatabaseMissing('events', [
                'name' => $event_name,
                'information' => $information,
                'start_date' => $event_date.' '.$start_time,
                'end_date' => $event_date.' '.$end_time,
                'max_people' => $max_people,
                'is_visible' =>  $is_visible,
            ]);
        }

    }

    /**
     * データプロバイダ
     * @see https://phpunit.readthedocs.io/ja/latest/writing-tests-for-phpunit.html#writing-tests-for-phpunit-data-providers
     *
     * @return array
     */
    public function storeDataProvider()
    {
        return [
            '正常に登録される' => ['イベント名', 'イベント詳細', '2022-06-15', '12:00', '13:30', '20', '1', 'success', []],
            'イベント名が無いバリデーションエラー' => ['', 'イベント詳細', '2022-06-15', '12:00', '13:30', '20', '1', 'error', ['event_name']],
        ];
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
