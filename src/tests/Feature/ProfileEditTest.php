<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * プロフィール編集画面に既存の情報が初期値として表示されている
     */
    public function test_profile_edit_page_shows_initial_values()
    {
        // 1. 前準備：既存データを持つユーザーを作成
        $user = User::factory()->create(['name' => '既存の名前']);
        $user->profile()->create([
            'profile_image' => 'initial_image.jpg',
            'post_code' => '111-2222',
            'address' => '東京都既存区1-2-3',
            'building_name' => '既存ビル',
        ]);

        // 2. プロフィール編集画面（URLは route:list の /mypage/profile に合わせる）にアクセス
        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);

        // 3. フォームの初期値としてデータが含まれているか確認
        $response->assertSee('既存の名前');
        $response->assertSee('111-2222');
        $response->assertSee('東京都既存区1-2-3');
        $response->assertSee('既存ビル');
        
        // 画像が表示（またはパスが含まれている）されているか
        $response->assertSee('initial_image.jpg');
    }
}