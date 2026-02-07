<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item; // ★追加
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile; 
use Illuminate\Support\Facades\Storage; 
use Tests\TestCase;

class ItemPublishTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // マスタデータの準備
        Category::create(['name' => 'ファッション']);
        Category::create(['name' => 'メンズ']);
        Condition::create(['name' => '良好']);
    }

    /**
     * 商品出品画面にて必要な情報が保存できる
     */
    public function test_can_publish_item()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // 出品データ（コントローラーのキー名 'categories' に合わせています）
        $itemData = [
            'name' => 'テスト商品',
            'description' => '商品の説明文です。',
            'price' => 5000,
            'condition_id' => 1,
            'categories' => [1, 2], 
            'brand' => 'テストブランド',
            'item_image' => UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg'),
        ];

        // ログインして出品実行
        $response = $this->actingAs($user)
                         ->post('/sell', $itemData);

        // 1. 出品後のリダイレクト
        $response->assertStatus(302);
        
        // 2. データベースに基本情報が保存されているか
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name'    => 'テスト商品',
            'price'   => 5000,
        ]);

        // 3. カテゴリが正しく紐づいているか
        $item = Item::where('name', 'テスト商品')->first();
        $this->assertCount(2, $item->categories); 
    }

    /**
     * 【追加】出品した商品がトップページ一覧に表示される
     * （自分が出品した商品を除外しない仕様の確認）
     */
    /**
     * 【修正】自分が出品した商品はトップページ一覧に表示されない
     */
    public function test_own_item_is_not_displayed_on_top_page()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $condition = Condition::first();
        
        // 1. 自分が出品した商品を作成
        $ownItem = Item::create([
            'user_id'      => $user->id,
            'condition_id' => $condition->id,
            'name'         => '自分の商品名',
            'brand'        => 'テストブランド',
            'price'        => 1000,
            'description'  => 'テスト説明',
            'image_paths'  => ['/storage/item_images/own.jpg'],
        ]);

        // 2. 他人が出品した商品を作成（比較用）
        $otherItem = Item::create([
            'user_id'      => $otherUser->id,
            'condition_id' => $condition->id,
            'name'         => '他人の商品名',
            'brand'        => 'テストブランド',
            'price'        => 2000,
            'description'  => 'テスト説明',
            'image_paths'  => ['/storage/item_images/other.jpg'],
        ]);

        // 3. ログインした状態でトップページ（おすすめタブ）へアクセス
        $response = $this->actingAs($user)->get('/?tab=recommend');

        // 4. 検証
        $response->assertStatus(200);
        
        // 【重要】他人の商品は表示されていることを確認
        $response->assertSee('他人の商品名');
        
        // 【重要】自分の商品は「表示されていない」ことを確認
        $response->assertDontSee('自分の商品名');
    }
}