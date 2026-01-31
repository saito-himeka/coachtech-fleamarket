<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile; // ★追加
use Illuminate\Support\Facades\Storage; // ★追加
use Tests\TestCase;

class ItemPublishTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
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

        // 出品データ
        $itemData = [
            'name' => 'テスト商品',
            'description' => '商品の説明文です。',
            'price' => 5000,
            'condition_id' => 1,
            'categories' => [1, 2], // ★ 'category_ids' を 'categories' に修正
            'brand' => 'テストブランド',
            'item_image' => UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg'),
        ];

        // ログインして出品実行
        $response = $this->actingAs($user)
                         ->post('/sell', $itemData);

        // ...以下、検証部分
        $response->assertStatus(302);
        
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name'    => 'テスト商品',
        ]);

        $item = \App\Models\Item::where('name', 'テスト商品')->first();
        $this->assertCount(2, $item->categories); // これでカウントが2になるはずです！
    }
}