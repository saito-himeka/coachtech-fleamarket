<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * お気に入りの登録・解除を切り替える
     */
    public function toggle(Item $item)
    {
        $user = Auth::user();

        if ($item->isFavoritedBy($user)) {
            $user->favoriteItems()->detach($item->id);
            $isFavorited = false;
        } else {
            $user->favoriteItems()->attach($item->id);
            $isFavorited = true;
        }

        // ここが Ajax 用の返却。JavaScriptにデータを渡します。
        return response()->json([
            'is_favorited' => $isFavorited,
            'count' => $item->favorites()->count()
        ]);
    }
}