<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * 商品名で部分一致検索ができる
     */
    public function test_can_search_items_by_keyword()
    {
        // 1. 検索にヒットさせたい商品
        Item::factory()->create(['name' => 'ターゲット商品']);
        
        // 2. 検索にヒットさせたくない商品
        Item::factory()->create(['name' => '無関係なもの']);

        // 検索キーワード「ターゲット」でアクセス
        $response = $this->get('/?keyword=ターゲット');

        $response->assertStatus(200);
        // ヒットすべき商品が表示されているか
        $response->assertSee('ターゲット商品');
        // ヒットすべきでない商品が表示されていないか
        $response->assertDontSee('無関係なもの');
    }
}