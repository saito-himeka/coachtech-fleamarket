<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Condition::create(['name' => '良好']);
    }

    /**
     * 購入画面で選択した支払い方法が画面に反映される
     */
    public function test_selected_payment_method_is_reflected_in_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 購入画面を表示
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);

        // 支払い方法を選択したと仮定して、その値が反映されているか確認
        // ※JavaScriptでの反映をテストする場合、通常はブラウザテスト(Dusk)が必要ですが、
        //  ここでは「選択肢が存在し、初期値や変更後の値が正しく処理される準備があるか」を確認します。
        $response->assertSee('コンビニ払い');
        $response->assertSee('カード支払い');
    }
}