<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * 商品をお気に入り登録できる
     */
    public function test_can_favorite_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ログインしてPOSTリクエスト（いいね実行）
        $response = $this->actingAs($user)->postJson("/item/{$item->id}/favorite");

        $response->assertStatus(200)
                 ->assertJson([
                     'is_favorited' => true,
                     'count' => 1
                 ]);

        // データベースに記録されているか確認
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * 商品のお気に入り登録を解除できる
     */
    public function test_can_unfavorite_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 最初からいいねしている状態を作る
        $user->favoriteItems()->attach($item->id);

        // ログインして再度POSTリクエスト（いいね解除）
        $response = $this->actingAs($user)->postJson("/item/{$item->id}/favorite");

        $response->assertStatus(200)
                ->assertJson([
                    'is_favorited' => false,
                    'count' => 0
                ]);

        // データベースから消えているか確認
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}