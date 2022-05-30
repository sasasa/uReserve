<?php

namespace Tests\Browser\Livewire;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class RegisterTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
    }
    /**
     * A Dusk test example.
     * @test
     * @return void
     */
    public function register_ページを表示できてページングされていること()
    {
        $that = $this;
        $this->browse(function (Browser $browser) use($that) {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $user3 = User::factory()->create();
            $user4 = User::factory()->create();
            $browser->visit(route('livewire-test.register'))
                    ->assertSee('livewireテスト')
                    ->assertSee($user1->name)
                    ->assertSee($user2->name)
                    ->assertSee($user3->name)
                    ->assertDontSee($user4->name)
                    // ページネーション2ページ目にアクセス
                    ->press('2')
                    ->waitForText($user4->name)
                    ->assertSee($user4->name)
                    ->assertDontSee($user1->name)
                    ->assertDontSee($user2->name)
                    ->assertDontSee($user3->name)
                    ;
        });
    }

    /**
     * A Dusk test example.
     * @test
     * @return void
     */
    public function register_削除できること()
    {
        $that = $this;
        $this->browse(function (Browser $browser) use($that) {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $user3 = User::factory()->create();
            $user4 = User::factory()->create();
            $browser->visit(route('livewire-test.register'))
                    ->assertSee('livewireテスト')
                    ->assertSee($user1->name)
                    ->assertSee($user2->name)
                    ->assertSee($user3->name)
                    ->assertDontSee($user4->name)
                    ->press("@delete_btn_{$user1->id}")
                    ->acceptDialog()
                    // 1ページ目の要素がなくなることで2ページ目にあった要素が1ページ目に繰り上がる
                    ->waitUntilMissing("@row_{$user1->id}")
                    ->assertDontSee($user1->name)
                    ->assertSee($user2->name)
                    ->assertSee($user3->name)
                    ->assertSee($user4->name)
                    ;
        });
    }
    /**
     * A Dusk test example.
     * @test
     * @return void
     */
    public function register_名前とメールとパスワードのバリデーションが効いていて更新できること()
    {
        $that = $this;
        $this->browse(function (Browser $browser) use($that) {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $user3 = User::factory()->create();
            $user4 = User::factory()->create();
            $that->assertDatabaseHas('users', [
                'id' => $user1->id,
                'name' => $user1->name,
            ]);
            $browser->visit(route('livewire-test.register'))
                    ->assertSee('livewireテスト')
                    ->assertSee($user1->name)
                    ->assertSee($user2->name)
                    ->assertSee($user3->name)
                    ->assertDontSee($user4->name)
                    ->pressAndWaitFor("@update_btn_{$user1->id}")
                    // nameとemailに値が入ること
                    ->assertInputValue('@name', $user1->name)
                    ->assertInputValue('@email', $user1->email)
                    // ->pause(1000)
                    // 名前は必須
                    ->typeSlowly('@name', ".......")
                    ->type('@name', "")
                    ->press("変更する")
                    ->waitForText("名前は、必ず指定してください。")
                    ->assertSee("名前は、必ず指定してください。")
                    // 名前は50文字以下
                    ->type('@name', "012345678901234567890123456789012345678901234567890")
                    ->waitForText("名前は、50文字以下にしてください。")
                    ->assertSee("名前は、50文字以下にしてください。")
                    // 名前を正しく変更
                    ->type('@name', $user1->name. "_test")
                    ->waitUntilMissing("@error_name")
                    ->assertDontSee("名前は、必ず指定してください。")
                    // メールアドレスは必須
                    ->typeSlowly('@email', ".......")
                    ->type('@email', "")
                    ->waitForText("メールアドレスは、必ず指定してください。")
                    ->assertSee("メールアドレスは、必ず指定してください。")
                    // メールアドレス形式かどうか
                    ->type('@email', "not_mailaddress_type")
                    ->waitForText("メールアドレスは、有効なメールアドレス形式で指定してください。")
                    ->assertSee("メールアドレスは、有効なメールアドレス形式で指定してください。")
                    // メールアドレス形式であったとしても50文字以下
                    ->type('@email', "01234567890123456789012345678901234567890@gmail.com")
                    ->waitForText("メールアドレスは、50文字以下にしてください。")
                    ->assertSee("メールアドレスは、50文字以下にしてください。")
                    // メールアドレスを正しく入力
                    ->type('@email', $user1->email)
                    ->waitUntilMissing("@error_email")
                    ->assertDontSee("メールアドレスは、50文字以下にしてください。")
                    // パスワードは必須
                    ->typeSlowly('@password', ".......")
                    ->type('@password', "")
                    ->waitForText("パスワードは、必ず指定してください。")
                    ->assertSee("パスワードは、必ず指定してください。")
                    // パスワードは8文字以上
                    ->type('@password', "testtes")
                    ->press("変更する")
                    ->waitForText("パスワードは、8文字以上にしてください。")
                    ->assertSee("パスワードは、8文字以上にしてください。")
                    // パスワードを正しく入力
                    ->type('@password', "testtest")
                    ->waitUntilMissing("@error_password")
                    ->assertDontSee("パスワードは、8文字以上にしてください。")
                    ->press("変更する")
                    // 正しく更新されていることを確認
                    ->waitForText($user1->name. "_test")
                    ->assertSee($user1->name. "_test")
                    ;
            $that->assertDatabaseHas('users', [
                'id' => $user1->id,
                'name' => $user1->name. "_test",
            ]);
        });
    }
}
