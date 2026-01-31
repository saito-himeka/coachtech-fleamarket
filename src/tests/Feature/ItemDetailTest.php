<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品詳細画面に必要な情報が表示される
     */
    public function test_item_detail_page_displays_all_necessary_information()
    {
        // 1. 前準備：状態とカテゴリを作成
        $condition = Condition::create(['name' => '新品同様']);
        $category = Category::create(['name' => '家電']);

        // 2. テスト用商品の作成
        $item = Item::factory()->create([
            'name' => '詳細テスト商品',
            'price' => 5000,
            'description' => 'これは詳細ページのテストです。',
            'condition_id' => $condition->id,
            'image_paths' => ['detail_test.jpg'],
        ]);
        
        // カテゴリを紐付け
        $item->categories()->attach($category->id);

        // 3. 商品詳細ページへアクセス
        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        // 4. 情報の確認
        $response->assertSee('詳細テスト商品');
        $response->assertSee('5,000');
        $response->assertSee('これは詳細ページのテストです。');
        $response->assertSee('新品同様');
        $response->assertSee('家電');
    }
}