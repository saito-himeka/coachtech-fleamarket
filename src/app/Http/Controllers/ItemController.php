<?php

namespace App\Http\Controllers;

use App\Models\Item; // Itemモデルを使用
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 商品一覧表示（検索機能・タブ切り替え対応）
     */
    public function index(Request $request)
    {
        // 1. パラメータの取得
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->input('keyword'); 

        // 2. ベースとなるクエリ（土台）の決定
        if ($tab === 'mylist') {
            // マイリストタブ：ログイン中なら「いいね」した商品、未ログインなら空
            if (Auth::check()) {
                $query = Auth::user()->favoriteItems();
            } else {
                return view('items.index', [
                    'items' => collect(), 
                    'tab' => $tab, 
                    'keyword' => $keyword
                ]);
            }
        } else {
            // おすすめタブ：全商品を対象にするクエリ
            $query = Item::query();
        }

        // 3. 検索キーワードがあれば絞り込みを追加
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        // 4. 共通の条件（購入情報の読み込み、最新順）を適用してデータ取得
        $items = $query->with('purchase')->latest()->get();

        // 5. ビューへ渡す（keywordも渡すと、検索窓に文字を保持できる）
        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    /**
     * 商品詳細表示
     */
    public function show($item_id)
    {
        // with() の中に 'favorites' と 'comments' を追加
        $item = Item::with(['condition', 'favorites', 'comments.user.profile'])->findOrFail($item_id);

        // 画像パスの変換ロジック（既存のもの）
        $images = is_array($item->image_paths) 
                    ? $item->image_paths 
                    : json_decode($item->image_paths, true);

        return view('items.show', compact('item', 'images'));
    }

    /**
     * 商品出品画面
     */
    public function create()
    {
        // DBから全カテゴリーを取得
        $categories = Category::all();
        $conditions = Condition::all();
        
        // viewに変数 $categories を渡す
        return view('items.create', compact('categories', 'conditions'));
    }

    /**
     * 商品出品処理
     */
    public function store(Request $request)
    {
        // 1. バリデーション
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|integer|min:0',
            'condition_id' => 'required|exists:conditions,id', // condition_idがDBに存在するか
            'description' => 'required',
            'item_image' => 'required|image',
        ]);

        // 2. 画像の保存
        $imagePath = null;
        if ($request->hasFile('item_image')) {
            $path = $request->file('item_image')->store('item_images', 'public');
            $imagePath = \Storage::url($path);
        }

        // 3. 商品の保存（一括代入）
        $item = Item::create([
            'user_id'      => auth()->id(),
            'condition_id' => $request->condition_id, // モデルの定義に合わせる
            'name'         => $request->name,
            'brand'        => $request->brand,
            'price'        => $request->price,
            'description'  => $request->description,
            'image_paths'  => [$imagePath],
        ]);

        // 4. カテゴリーの紐付け（多対多のリレーション）
        if ($request->has('categories')) {
            // もしBladeのvalueがID（数値）ならそのままattachできます
            // もし「名前」を送っている場合は、名前からIDを探す処理が必要です
            // ここではIDが送られてきている前提の書き方です：
            $item->categories()->attach($request->categories);
        }

        return redirect()->route('item.index')->with('message', '商品を出品しました！');
    }
}