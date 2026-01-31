<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 3-1: ログアウトができる
     */
    public function test_user_can_logout()
    {
        // 1. テスト用ユーザーを作成
        $user = User::factory()->create();

        // 2. そのユーザーとして「ログインした状態」で操作を開始する 
        // 3. ログアウト処理（POST）を実行する 
        $response = $this->actingAs($user)->post('/logout');

        // 4. ログアウト後に、ユーザーが「ゲスト状態」になっているか確認
        $this->assertGuest();

        // 5. ログアウト後の遷移先（通常はトップページ等）を確認
        $response->assertRedirect('/'); 
    }
}