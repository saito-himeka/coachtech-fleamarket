<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * 商品を購入できる（購入完了後にトップページへ遷移し、Sold表示になる）
     */
    public function test_can_purchase_item()
    {
        $user = User::factory()->create();
        // 配送先が必要なのでプロフィールも作成しておく
        $user->profile()->create([
            'post_code' => '123-4567',
            'address' => '東京都渋谷区...',
        ]);

        $item = Item::factory()->create(['name' => '購入テスト商品']);

        // 直接 success メソッド（保存処理）を叩く
        // ※ 本来は Stripe がここへリダイレクトさせてくるのを、テストで再現します
        $response = $this->actingAs($user)
                         ->get("/purchase/success/{$item->id}");

        // 1. トップページへリダイレクトされるか
        $response->assertStatus(302);
        $response->assertRedirect(route('item.index'));

        // 2. データベースに購入記録があるか
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 2, // successメソッドで固定されている値
        ]);

        // 3. 商品一覧画面で「Sold」が表示されているか
        $response = $this->get('/');
        $response->assertSee('購入テスト商品');
        $response->assertSee('SOLD');
    }
}