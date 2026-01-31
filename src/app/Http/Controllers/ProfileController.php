<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{

    public function __construct()
    {
        // このコントローラー内の全てのメソッドを実行する前に、ログインチェックを行う
        $this->middleware('auth');
    }

    // プロフィール編集画面の表示
    public function edit()
    {
        $user = Auth::user();
        return view('profiles.edit', compact('user'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {

        $user = Auth::user();
        
        // ユーザー名の更新
        $user->update([
            'name' => $request->name
        ]);

        // プロフィール情報の更新（画像がある場合とない場合）
        $profileData = $request->only(['post_code', 'address', 'building_name']);

        if ($request->hasFile('profile_image')) {
            // 画像を storage/app/public/profile_images に保存
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $profileData['profile_image'] = $path;
        }

        // updateOrCreate: プロフィールがなければ作成、あれば更新
        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return redirect()->route('profile.show')->with('message', 'プロフィールを更新しました');
    }


    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'sell'); // デフォルトは「出品(sell)」

        if ($tab === 'buy') {
            // 「購入した商品」を取得（Purchaseモデル経由でItemを取得）
            // ※pluck('item') を使うと、購入履歴から商品データだけを抽出できます
            $items = Purchase::where('user_id', $user->id)
                    ->with('item')
                    ->get()
                    ->pluck('item');
        } else {
            // 「出品した商品」を取得
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('profiles.show', compact('user', 'items', 'tab'));
    }
}