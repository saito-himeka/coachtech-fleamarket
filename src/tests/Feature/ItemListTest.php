<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 4-1: 全商品を取得できる
     */
    
    public function test_can_retrieve_all_items()
    {
        $this->withoutExceptionHandling();
        // テスト用商品を2つ作成
        Item::factory()->create(['name' => '商品A']);
        Item::factory()->create(['name' => '商品B']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
    }

    /**
     * 4-2: 購入済み商品は「Sold」と表示される
     */
    public function test_sold_label_is_displayed_for_purchased_items()
    {
        $this->withoutExceptionHandling();
        $item = Item::factory()->create(['name' => '売り切れ商品']);
        $user = User::factory()->create();

        // 商品を購入済みにする（Purchaseレコードを作成）
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => '1',
            'post_code' => '123-4567',
            'address' => '東京都渋谷区...',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        // 商品名が表示され、かつ「Sold」という文字が含まれているか確認 
        $response->assertSee('売り切れ商品');
        $response->assertSee('sold', false);
    }

    /**
     * 4-3: 自分が出品した商品は表示されない
     */
    public function test_own_items_are_not_displayed_in_list()
    {
        $this->withoutExceptionHandling();
        $me = User::factory()->create();
        $others = User::factory()->create();

        // 自分が出品した商品
        $myItem = Item::factory()->create([
            'user_id' => $me->id,
            'name' => '私が出品した商品'
        ]);

        // 他人が出品した商品
        $otherItem = Item::factory()->create([
            'user_id' => $others->id,
            'name' => '他人が出品した商品'
        ]);

        // ログインしてトップページを表示
        $response = $this->actingAs($me)->get('/');

        // 他人の商品は見えるが、自分の商品は見えないことを確認 
        $response->assertSee('他人が出品した商品');
        $response->assertDontSee('私が出品した商品');
    }
}