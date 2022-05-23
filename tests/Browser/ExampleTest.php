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
    public function test_トップページアクセス()
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
                    ->assertPathIs('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('ログイン')
                    ->assertPathIs("/{$event->id}")
                    ->assertSee($event->name)
                    ->assertSee($event->information)
                    ->select('reserved_people', '1')
                    ->press('予約する')
                    ->assertPathIs("/dashboard")
                    ->assertSee('登録OKです')
                    ->waitForText($event->name)
                    ->clickLink($event->name)
                    ->assertPathIs("/{$event->id}")
                    ->assertSee($event->name)
                    ->assertSee($event->information)
                    ->assertSee('このイベントは既に予約済みです。')
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
}
