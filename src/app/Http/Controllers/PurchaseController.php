<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PurchaseController extends Controller
{
    // 1. 購入画面の表示（統合・強化版）
    public function create($item_id)
    {
        // 商品情報をデータベースから取得（リレーションも含めておくとスムーズです）
        $item = Item::findOrFail($item_id);
        
        // すでに売り切れているかチェック（必要に応じて）
        if ($item->purchase) {
            return redirect()->route('item.show', $item_id)->with('error', 'この商品は既に売り切れています。');
        }

        // ビューには $item を渡すように変更
        return view('purchases.create', compact('item'));
    }

    // 2. 送付先住所変更画面の表示（元のコードを維持）
    public function editAddress($item_id)
    {
        $user = Auth::user();
        return view('purchases.address_edit', compact('user', 'item_id'));
    }

    // 3. 送付先住所の更新処理（元のコードを維持）
    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'post_code' => ['required', 'string', 'max:8'],
            'address' => ['required', 'string', 'max:255'],
            'building_name' => ['nullable', 'string', 'max:255'], // buildingは空でも良いようにnullableに
        ]);

        $user = Auth::user();
        
        // プロフィールを更新（リレーション先の profile テーブルを更新）
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['post_code', 'address', 'building_name'])
        );

        return redirect()->route('purchases.create', ['item_id' => $item_id]);
    }

    // 4. 購入確定処理（今後ここに作成します）
    public function store(Request $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // 1. バリデーション（支払い方法の選択チェック）
        $request->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => '支払い方法を選択してください。',
        ]);

        // 2. 配送先チェック
        if (!$user->profile || !$user->profile->address) {
            return redirect()->back()->with('error', '配送先を登録してください。');
        }

        // 3. 売り切れチェック
        if ($item->purchase) {
            return redirect()->route('item.index')->with('error', 'この商品はすでに売り切れです。');
        }

        // --- ここから Stripe 処理 ---

        // Stripeのシークレットキーをセット
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Stripe Checkoutセッションを作成
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'], // 今回はカード決済
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // 決済成功時のリダイレクト先（後でDB保存処理を作ります）
            'success_url' => route('purchases.success', ['item_id' => $item->id]), 
            'cancel_url' => route('purchases.create', ['item_id' => $item->id]),
        ]);

        // Stripeの決済ページへ飛ばす
        return redirect($session->url, 303);
    }

    public function success(Request $request, $item_id)
    {
        $user = \Auth::user();
        $item = Item::findOrFail($item_id);

        // 二重保存防止チェック（すでに購入データがあればトップへ）
        if ($item->purchase) {
            return redirect()->route('item.index');
        }

        // 購入情報の保存（以前 store に書いていた処理をここに移動）
        \App\Models\Purchase::create([
            'item_id'        => $item->id,
            'user_id'        => $user->id,
            // 支払い方法はStripe固定なので 2（カード）など
            'payment_method' => 2, 
            'post_code'      => $user->profile->post_code,
            'address'        => $user->profile->address,
            'building_name'  => $user->profile->building_name,
        ]);

        return redirect()->route('item.index')->with('message', 'ご購入ありがとうございました！');
    }
}