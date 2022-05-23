<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class ManagerTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function test_ログインせずにアクセスすると403()
    {
        $response = $this->get(route('events.index'));
        $response->assertStatus(403);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function test_権限がないユーザーでアクセスすると403()
    {
        $user = User::factory()->create([
            'role' => 9,
        ]);
        $this->actingAs($user);
        $response = $this->get(route('events.index'));
        $response->assertStatus(403);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function test_権限があるユーザーでアクセスすると200()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $event = Event::factory()->create();
        $this->actingAs($user, 'web');
        $response = $this->get(route('events.index'));
        $response->assertStatus(200);

        $response = $this->get(route('events.show', ['event' => $event]));
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     * @test
     * @dataProvider dataProvider_イベント更新
     * @return void
     */
    public function test_イベント更新($event_name, $information, $event_date, 
        $start_time, $end_time, $max_people, $is_visible, $result, $sessionError)
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $event = Event::factory()->create();
        $response = $this->actingAs($user, 'web')->
            from(route('events.edit', ['event' => $event]))->
            patch(route('events.update', ['event' => $event]), [
                'event_name' => $event_name,
                'information' => $information,
                'event_date' => $event_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'max_people' => $max_people,
                'is_visible' => $is_visible,
            ]);
        if($result === 'success') {
            $response->assertRedirect(route('events.index'));
            $this->assertDatabaseHas('events', [
                'id' => $event->id,
                'name' => $event_name,
                'information' => $information,
                'start_date' => $event_date.' '.$start_time,
                'end_date' => $event_date.' '.$end_time,
                'max_people' => $max_people,
                'is_visible' =>  $is_visible,
            ]);
        }
        if ($result === 'error') {
            // Formで失敗したときって元のページに戻りますよね
            $response->assertRedirect(route('events.edit', ['event' => $event]));
            /**
             * @see https://readouble.com/laravel/6.x/ja/http-tests.html#assert-session-has-errors
             * @see https://qiita.com/iakio/items/f7a1064235c39db3f392
             */
            $response->assertSessionHasErrors($sessionError);
            $this->assertDatabaseMissing('events', [
                'id' => $event->id,
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
     * A basic feature test example.
     * @test
     * @dataProvider dataProvider_イベント更新
     * @return void
     */
    public function test_イベント新規作成($event_name, $information, $event_date, 
        $start_time, $end_time, $max_people, $is_visible, $result, $sessionError)
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $response = $this->actingAs($user, 'web')->
            from(route('events.create'))->
            post(route('events.store'), [
                'event_name' => $event_name,
                'information' => $information,
                'event_date' => $event_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'max_people' => $max_people,
                'is_visible' => $is_visible,
            ]);
        if($result === 'success') {
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
        if ($result === 'error') {
            $response->assertRedirect(route('events.create'));
            /**
             * @see https://readouble.com/laravel/6.x/ja/http-tests.html#assert-session-has-errors
             * @see https://qiita.com/iakio/items/f7a1064235c39db3f392
             */
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
    public function dataProvider_イベント更新()
    {
        return [
            // 'event_name','information','event_date','start_time','end_time','max_people','is_visible',$result(success || error), $sessionError
            "すべて正常" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '12:00', 18, '1', 'success', []],
            "定員数が20" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '12:00', 20, '1', 'success', []],
            "定員数が20以上" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '12:00', 21, '1', 'error', ['max_people']],
            "定員数が数字でない" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '11:00', 'ImpossibleValue', '1', 'error', ['max_people']],
            "終了時間が開始時間と同一" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '11:00', 18, '1', 'error', ['end_time']],
            "イベント日付が存在しない日付" => ['イベント名', 'イベント詳細', '2022-02-31', '11:00', '12:00', 18, '1', 'error', ['event_date']],
            "イベント詳細が200文字" => ['イベント名', '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890', '2022-05-23', '11:00', '12:00', 18, '1', 'success', []],
            "イベント詳細が201文字" => ['イベント名', '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901', '2022-05-23', '11:00', '12:00', 18, '1', 'error', ['information']],
            "イベント名50文字" => ['イベント名イベント名イベント名イベント名イベント名イベント名イベント名イベント名イベント名イベント名', 'イベント詳細', '2022-05-23', '11:00', '12:00', 18, '1', 'success', []],
            "イベント名51文字" => ['イベント名イベント名イベント名イベント名イベント名イベント名イベント名イベント名イベント名イベント名イ', 'イベント詳細', '2022-05-23', '11:00', '12:00', 18, '1', 'error', ['event_name']],
            "イベント名が無い" => ['', 'イベント詳細', '2022-05-23', '11:00', '12:00', 18, '1', 'error', ['event_name']],
            "イベント詳細が無い" => ['イベント名', '', '2022-05-23', '11:00', '12:00', 18, '1', 'error', ['information']],
            "イベント日付が無い" => ['イベント名', 'イベント詳細', '', '11:00', '12:00', 18, '1', 'error', ['event_date']],
            "開始時間が無い" => ['イベント名', 'イベント詳細', '2022-05-23', '', '12:00', 18, '1', 'error', ['start_time']],
            "終了時間が無い" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '', 18, '1', 'error', ['end_time']],
            "定員数が無い" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '12:00', null, '1', 'error', ['max_people']],
            "表示非表示が無い" => ['イベント名', 'イベント詳細', '2022-05-23', '11:00', '12:00', 18, '', 'error', ['is_visible']],
        ];
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function test_イベント更新重複するとき()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $event1 = Event::factory()->create([
            'start_date' => '2021-05-23 11:00',
            'end_date' => '2021-05-23 12:00',
        ]);
        $event2 = Event::factory()->create([
            'start_date' => '2022-05-23 11:00',
            'end_date' => '2022-05-23 12:00',
        ]);
        $response = $this->actingAs($user, 'web')->
            from(route('events.edit', ['event' => $event1]))->
            patch(route('events.update', ['event' => $event1]), [
                'event_name' => 'イベント名',
                'information' => 'イベント詳細',
                'event_date' => '2022-05-23',
                'start_time' => '11:00',
                'end_time' => '12:00',
                'max_people' => 18,
                'is_visible' => '1',
            ]);
        $response->assertRedirect(route('events.edit', ['event' => $event1]));
        /**
         * @see https://readouble.com/laravel/6.x/ja/http-tests.html#assert-session-has-errors
         * @see https://qiita.com/iakio/items/f7a1064235c39db3f392
         */
        $response->assertSessionHasErrors(['start_time']);
        $this->assertDatabaseMissing('events', [
            'id' => $event1->id,
            'name' => 'イベント名',
            'information' => 'イベント詳細',
            'start_date' => '2022-05-23 11:00',
            'end_date' => '2022-05-23 12:00',
            'max_people' => 18,
            'is_visible' =>  '1',
        ]);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function test_イベント新規作成重複するとき()
    {
        $user = User::factory()->create([
            'role' => 5,
        ]);
        $event = Event::factory()->create([
            'start_date' => '2022-05-23 11:00',
            'end_date' => '2022-05-23 12:00',
        ]);
        $response = $this->actingAs($user, 'web')->
            from(route('events.create'))->
            post(route('events.store'), [
                'event_name' => 'イベント名',
                'information' => 'イベント詳細',
                'event_date' => '2022-05-23',
                'start_time' => '11:00',
                'end_time' => '12:00',
                'max_people' => 18,
                'is_visible' => '1',
            ]);
        $response->assertRedirect(route('events.create'));
        /**
         * @see https://readouble.com/laravel/6.x/ja/http-tests.html#assert-session-has-errors
         * @see https://qiita.com/iakio/items/f7a1064235c39db3f392
         */
        $response->assertSessionHasErrors(['start_time']);
        $this->assertDatabaseMissing('events', [
            'name' => 'イベント名',
            'information' => 'イベント詳細',
            'start_date' => '2022-05-23 11:00',
            'end_date' => '2022-05-23 12:00',
            'max_people' => 18,
            'is_visible' =>  '1',
        ]);
    }
}
