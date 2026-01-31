<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function test_updated_address_is_reflected_in_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. 住所変更実行（PATCHリクエスト）
        $addressData = [
            'post_code' => '999-9999',
            'address' => '東京都新宿区テスト1-1',
            'building_name' => 'テストビル101',
        ];

        $this->actingAs($user)
             ->patch("/purchase/address/{$item->id}", $addressData);

        // 2. 購入画面を開いて、新しい住所が表示されているか確認
        $response = $this->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('999-9999');
        $response->assertSee('東京都新宿区テスト1-1');
        $response->assertSee('テストビル101');
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_purchased_item_is_linked_to_shipping_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. プロフィールに住所を設定しておく
        $user->profile()->create([
            'post_code' => '888-8888',
            'address' => '大阪府大阪市テスト2-2',
            'building_name' => 'サンプルマンション202',
        ]);

        // 2. 購入処理（successメソッド）を実行
        $this->actingAs($user)
             ->get("/purchase/success/{$item->id}");

        // 3. purchasesテーブルに、その時の住所が保存されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'post_code' => '888-8888',
            'address' => '大阪府大阪市テスト2-2',
            'building_name' => 'サンプルマンション202',
        ]);
    }
}