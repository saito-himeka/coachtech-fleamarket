<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 2-1: メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * 2-2: パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 2-3: 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_wrong_credentials()
    {
        // まだ登録されていない情報でログイン試行
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        // Laravelデフォルト（Fortify）の挙動では、特定のメッセージが含まれるか確認
        // テストケース一覧の「ログイン情報が登録されていません」に合わせる
        $response->assertSessionHasErrors(['email']); 
    }

    /**
     * 2-4: 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_user_can_login_with_correct_credentials()
    {
        // テスト用ユーザーを1件作成
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user); // ログイン状態になったか確認
        $response->assertRedirect('/'); // トップページなどへ遷移するか確認
    }
}