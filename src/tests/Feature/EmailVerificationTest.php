<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録時にメール認証通知（メール）が送信されるか
     */
    public function test_verification_email_is_sent_on_registration()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 登録されたユーザーに対して、VerifyEmail通知が送られたことを確認
        Notification::assertSentTo(
            User::where('email', 'verify@example.com')->first(),
            VerifyEmail::class
        );
    }

    /**
     * 未認証ユーザーが、認証が必要なページ（購入画面など）にアクセスした際にリダイレクトされるか
     */
    public function test_unverified_user_is_redirected_to_verify_notice_page()
    {
        // 認証されていない状態のユーザーを作成
        $user = User::factory()->unverified()->create();

        // 認証が必要なページ（例として購入画面）へアクセス
        // ※ルートに verified ミドルウェアがかかっていることが前提です
        $response = $this->actingAs($user)->get('/purchase/1');

        // 認証を促す画面へリダイレクトされることを確認
        $response->assertRedirect('/email/verify');
    }
}