<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // 商品作成に必要なConditionを作成
        Condition::create(['name' => '良好']);
    }

    /**
     * 「マイリスト」には自分がお気に入り登録した商品のみが表示される
     */
    public function test_only_favorite_items_are_displayed_in_mylist()
    {
        $user = User::factory()->create();
        
        // 1. お気に入りした商品
        $favoriteItem = Item::factory()->create(['name' => 'お気に入り商品']);
        $user->favoriteItems()->attach($favoriteItem->id);

        // 2. お気に入りしていない商品
        $otherItem = Item::factory()->create(['name' => '通常の商品']);

        // ログインしてマイリストタブを表示
        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('お気に入り商品');
        $response->assertDontSee('通常の商品');
    }

    /**
     * 未ログイン状態でマイリストにアクセスすると何も表示されない（または空の状態で表示）
     */
    public function test_mylist_is_empty_when_not_logged_in()
    {
        $item = Item::factory()->create(['name' => 'テスト商品']);

        // 未ログインでマイリストタブを表示
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('テスト商品');
    }
}