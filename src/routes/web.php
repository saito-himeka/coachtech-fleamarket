<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 商品一覧画面（トップページ）: ログイン不要で閲覧可能
Route::get('/', [ItemController::class, 'index'])->name('item.index');

// 商品詳細画面: ログイン不要で閲覧可能にするのが一般的（購入時にログインを求める）
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])
    ->name('favorite.toggle')
    ->middleware('auth');

Route::post('/item/{item}/comment', [CommentController::class, 'store'])
    ->name('comment.store')
    ->middleware('auth');

// ログインしているユーザーのみアクセス可能
Route::middleware(['auth', 'verified'])->group(function () {

    // マイページ関連
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.show');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 購入関連
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchases.store'); // 購入実行ルートを追加
    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchases.success');

    // 送付先住所変更
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchases.address.edit');
    Route::patch('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchases.address.update');

    // 出品関連
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');
});

