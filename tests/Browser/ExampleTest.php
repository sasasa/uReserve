<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ExampleTest extends DuskTestCase
{
    use RefreshDatabase;
    
    /**
     * A basic browser test example.
     * @test
     * @return void
     */
    public function test_トップページアクセス()
    {
        $event = Event::factory()->create([
            'start_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30),
            'end_date' => Carbon::now()->addDays(3)->setHour(13)->setMinute(30)->addHours(2),
            'is_visible' => 1,
        ]);
        $this->browse(function (Browser $browser) use($event) {
            $browser->visit('/')
                    ->waitForText('日付を選択してください。本日から最大30日先まで選択可能です。')
                    ->assertSee('日付を選択してください。本日から最大30日先まで選択可能です。');
                    // ->waitForText($event->name)
                    // ->clickLink($event->name)
                    // ->assertPathIs('/login');
            
        });
    }
}
