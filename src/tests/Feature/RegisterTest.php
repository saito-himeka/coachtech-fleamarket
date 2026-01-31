<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase; // テストごとにDBを綺麗にする

    /**
     * ID 1: 名前が入力されていない場合、バリデーションメッセージが表示される [cite: 1]
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '', // 名前を空にする [cite: 1]
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302); // バリデーションエラーでリダイレクトされる [cite: 1]
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']); // 期待挙動の確認 [cite: 1]
    }

    /**
     * ID 1: 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される [cite: 1]
     */
    public function test_user_can_register_and_redirect_to_profile()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 会員情報が登録されているか確認 [cite: 1]
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'newuser@example.com',
        ]);

        // プロフィール設定画面（/mypage/profile）に遷移するか確認 [cite: 1]
        $response->assertRedirect('/email/verify'); 
    }
}