<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Event;
use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class ExampleTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:refresh');
    }

    /**
     * A basic browser test example.
     * @test
     * @return void
     */
    public function test_ログインして予約して再度予約できないことを確認した後キャンセルする()
    {
        $that = $this;
        $this->browse(function (Browser $browser) use($that) {
            $event = Event::factory()->create([
                'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
                'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
                'is_visible' => true,
            ]);
            $user = User::factory()->create();
            $that->assertDatabaseHas('events', [
                'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
                'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
                'is_visible' => 1,
            ]);

            $browser->visit('/')
                    ->waitForText('日付を選択してください。本日から最大30日先まで選択可能です。')
                    ->assertSee('日付を選択してください。本日から最大30日先まで選択可能です。')
                    ->waitForText($event->name)
                    ->clickLink($event->name)
                    // ログインせずに予約機能にアクセスするとlogin画面に飛ばされる
                    ->assertPathIs('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('ログイン')
                    ->assertPathIs("/{$event->id}")
                    ->assertSee($event->name)
                    ->assertSee($event->information)
                    ->select('reserved_people', '1')
                    ->press('予約する')
                    // 予約できている
                    ->assertPathIs("/dashboard")
                    ->assertSee('登録OKです')
                    ->waitForText($event->name)
                    ->clickLink($event->name)
                    ->assertPathIs("/{$event->id}")
                    ->assertSee($event->name)
                    ->assertSee($event->information)
                    ->assertSee('このイベントは既に予約済みです。')
                    // 予約済みの確認後マイページから予約を確認する
                    ->clickLink('マイページ')
                    ->assertPathIs("/mypage")
                    ->assertSee($event->name)
                    ->clickLink($event->name)
                    ->assertPathIs("/mypage/{$event->id}")
                    ->assertSee($event->name)
                    ->assertSee($event->information)
                    ->clickLink('キャンセルする')->acceptDialog()
                    ->assertPathIs("/dashboard")
                    ->assertSee('キャンセルできました');
        });
    }

    /**
     * A basic browser test example.
     * @test
     * @return void
     */
    public function test_ログインしてカレンダーを月末に操作する()
    {
        $that = $this;
        $this->browse(function (Browser $browser) use($that) {
            $event = Event::factory()->create([
                'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
                'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
                'is_visible' => true,
            ]);
            $user = User::factory()->create();
            $that->assertDatabaseHas('events', [
                'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
                'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
                'is_visible' => 1,
            ]);
            $endOfMonth = Carbon::now()->setDay(31)->format('n月 d, Y');
            $endOfMonthJp = Carbon::now()->setDay(31)->format('m月d日');
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('ログイン')
                    ->assertPathIs('/dashboard')
                    ->click('#calendar')
                    ->assertFocused('#calendar')
                    // カレンダーから月末を選択する
                    ->click("span[aria-label='{$endOfMonth}']")
                    ->waitForText($endOfMonthJp)
                    ->assertSee($endOfMonthJp);
        });
    }

    /**
     * A basic browser test example.
     * @test
     * @return void
     */
    public function test_2人が同一イベントに同時に申し込んだときに定員を超えないこと()
    {
        $that = $this;
        $this->browse(function (Browser $first, Browser $second) use($that) {
            $event = Event::factory()->create([
                'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
                'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
                'is_visible' => true,
            ]);
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $that->assertDatabaseHas('events', [
                'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->format('Y-m-d H:i:00'),
                'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2)->format('Y-m-d H:i:00'),
                'is_visible' => 1,
            ]);
            // アクセスして予約ページを開いた状態
            $second->loginAs(User::find($user2->id))
                    ->visit('/dashboard')
                    ->waitForText($event->name)
                    ->clickLink($event->name)
                    ->assertPathIs("/{$event->id}")
                    ->assertSee($event->name)
                    ->assertSee($event->information);

            // 僅差でmax-1人の予約をした
            $first->loginAs(User::find($user1->id))
                    ->visit('/dashboard')
                    ->waitForText($event->name)
                    ->clickLink($event->name)
                    ->assertPathIs("/{$event->id}")
                    ->assertSee($event->name)
                    ->assertSee($event->information)
                    ->select('reserved_people', $event->max_people - 1)
                    ->press('予約する')
                    ->assertPathIs("/dashboard")
                    ->assertSee('登録OKです');

            // max-1人の予約をしたら予約数チェックに引っ掛かる
            $second->select('reserved_people', $event->max_people - 1)
                    ->press('予約する')
                    ->assertPathIs("/{$event->id}")
                    // 人数制限に引っ掛かる
                    ->assertSee('この人数は予約できません。')
                    ->select('reserved_people', 1)
                    ->press('予約する')
                    ->assertPathIs("/dashboard")
                    ->assertSee('登録OKです');
        });
    }
}
