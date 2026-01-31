<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * プロフィールページで必要な情報（基本情報・出品・購入）が取得・表示できる
     */
    public function test_user_can_see_profile_and_item_lists()
    {
        $user = User::factory()->create(['name' => 'テスト太郎']);
        
        // 修正点1: カラム名を profile_image に合わせる
        $user->profile()->create([
            'profile_image' => 'test_profile.jpg',
            'post_code' => '123-4567',
            'address' => '東京都...',
        ]);

        // 1. 自分が出品した商品
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '私が出品した商品',
            'image_paths' => ['test_image.jpg']
        ]);

        // 2. 自分が購入した商品
        $otherItem = Item::factory()->create(['name' => '私が購入した商品']);
        \App\Models\Purchase::create([
            'user_id' => $user->id,
            'item_id' => $otherItem->id,
            'payment_method' => 1,
            'post_code' => '123-4567',
            'address' => '東京都...',
        ]);

        // --- 検証1: プロフィールと出品商品 ---
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('test_profile.jpg'); // これで画像パスが表示されるはずです
        $response->assertSee('私が出品した商品');

        // --- 検証2: 購入した商品（タブ切り替え） ---
        // 修正点2: 期待挙動に合わせて購入した商品タブの表示を確認
        $response = $this->get('/mypage?tab=buy');
        $response->assertStatus(200);
        $response->assertSee('私が購入した商品');
    }
}