@extends('layouts.app')

@section('title', '商品一覧 - COACHTECH')

@section('content')
<div class="top-nav">
    <div class="top-nav-inner">
        {{-- request('tab') が 'mylist' ではない時はすべて「おすすめ」をアクティブにする --}}
        <a href="{{ route('item.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}" 
        class="tab-item {{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
        
        <a href="{{ route('item.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}" 
        class="tab-item {{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>
</div>

<div class="top-container">
    <div class="product-grid">
        @foreach($items as $item)
        <div class="product-card">
            {{-- 商品詳細画面へのリンク --}}
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <div class="product-image">
                    @if($item->image_paths && count($item->image_paths) > 0)
                        {{-- 配列の最初の画像を表示 --}}
                        <img src="{{ $item->image_paths[0] }}" alt="{{ $item->name }}">
                    @else
                        {{-- 画像がない場合のプレースホルダー --}}
                        <img src="https://via.placeholder.com/300" alt="No Image">
                    @endif

                    {{-- 売却済み（purchaseレコードが存在する）場合の表示 --}}
                    @if($item->purchase)
                        <span class="sold-label">SOLD</span>
                    @endif
                </div>
                <div class="product-info">
                    <p class="product-name">{{ $item->name }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection