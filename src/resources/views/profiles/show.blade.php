@extends('layouts.app')

@section('content')
<div class="mypage-container">
    {{-- ユーザー情報セクション --}}
    <div class="user-info">
        <div class="user-flex">
            <div class="user-avatar">
                @if(Auth::user()->profile && Auth::user()->profile->profile_image)
                    <img src="{{ asset('storage/' . Auth::user()->profile->profile_image) }}" alt="ユーザー画像">
                @else
                    <div class="default-circle"></div>
                @endif
            </div>
            <h2 class="user-name">{{ Auth::user()->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="btn-edit-profile">プロフィールを編集</a>
        </div>
    </div>

    {{-- タブ切り替えセクション --}}
<div class="tabs">
    <a href="{{ route('profile.show', ['tab' => 'sell']) }}" 
       class="tab-item {{ $tab == 'sell' ? 'active' : '' }}">出品した商品</a>
    
    <a href="{{ route('profile.show', ['tab' => 'buy']) }}" 
       class="tab-item {{ $tab == 'buy' ? 'active' : '' }}">購入した商品</a>
</div>

{{-- 商品一覧セクション --}}
<div class="product-grid">
    @forelse($items as $item)
        <div class="product-item">
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <div class="product-image">
                    @if($item->image_paths && count($item->image_paths) > 0)
                        <img src="{{ $item->image_paths[0] }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <p>No Image</p>
                    @endif

                    {{-- 購入済み(SOLD)表示（共通のパーツがあればそれを使います） --}}
                    @if($item->purchase)
                        <span class="sold-label"></span>
                    @endif
                </div>
                <p class="product-name">{{ $item->name }}</p>
            </a>
        </div>
    @empty
        <p style="grid-column: 1/-1; text-align: center; color: #888; margin-top: 20px;">
            {{ $tab == 'buy' ? '購入した商品はありません' : '出品した商品はありません' }}
        </p>
    @endforelse
</div>
</div>
@endsection