<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * ログイン済みのユーザーはコメントを投稿できる
     */
    public function test_logged_in_user_can_post_a_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // コメント内容
        $commentData = ['comment' => 'これはテストコメントです。'];

        // ログインして投稿（POST）
        $response = $this->actingAs($user)
                         ->post("/item/{$item->id}/comment", $commentData);

        // 投稿後は元のページ（または詳細ページ）にリダイレクトされるはず
        $response->assertStatus(302);
        
        // データベースに保存されているか
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'これはテストコメントです。',
        ]);

        // 詳細画面で投稿したコメントが見えるか
        $this->get("/item/{$item->id}")
             ->assertSee('これはテストコメントです。');
    }

    /**
     * 未ログインユーザーはコメント投稿できない（ログイン画面へリダイレクト）
     */
    public function test_guest_user_cannot_post_a_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comment", ['comment' => 'ゲストのコメント']);

        $response->assertRedirect('/login');
    }
}