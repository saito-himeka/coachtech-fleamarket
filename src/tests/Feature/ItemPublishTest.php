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
    public function test_published_item_is_displayed_on_top_page()
    {
        $user = User::factory()->create();

        $condition = Condition::first();
        
        // 1. 商品を作成（自分が出品した状態にする）
        $item = Item::create([
            'user_id'      => $user->id,
            'condition_id' => $condition->id,
            'name'         => 'トップページ表示確認商品',
            'brand'        => 'テストブランド',
            'price'        => 1000,
            'description'  => 'テスト説明',
            'image_paths'  => ['/storage/item_images/test.jpg'],
        ]);

        // 2. ログインした状態でトップページへアクセス
        $response = $this->actingAs($user)->get('/');

        // 3. 自分の商品名が画面に表示されているか確認
        $response->assertStatus(200);
        $response->assertSee('トップページ表示確認商品');
    }
}