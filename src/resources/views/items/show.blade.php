@extends('layouts.app')

@section('title', $item->name . ' - COACHTECH')

@section('content')
<div class="item-detail-container">
    {{-- 左側：画像エリア --}}
    <div class="item-image-box">
        <div class="main-image">
            @if($item->image_paths && count($item->image_paths) > 0)
                <img src="{{ $item->image_paths[0] }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                No Image
            @endif
        </div>
    </div>

    {{-- 右側：詳細情報エリア --}}
    <div class="item-info-box">
        <h1 class="item-name">{{ $item->name }}</h1>
        <p class="brand-name">{{ $item->brand ?? 'ブランドなし' }}</p>
        
        <p class="item-price">
            ¥{{ number_format($item->price) }}<span>(税込)</span>
        </p>

        {{-- 統計アイコン（お気に入り・コメント数） --}}
        <div class="item-stats">
            <div class="stat-item">
                {{-- formは残しますが、submitをJavaScriptで止めます --}}
                <button type="button" id="favorite-btn" data-item-id="{{ $item->id }}" class="favorite-btn">
                    @if($item->isFavoritedBy(Auth::user()))
                        <img src="{{ asset('img/ハートロゴ_ピンク.png') }}" id="heart-icon" class="heart-icon">
                    @else
                        <img src="{{ asset('img/ハートロゴ_デフォルト.png') }}" id="heart-icon" class="heart-icon">
                    @endif
                </button>
                <span class="count" id="favorite-count">{{ $item->favorites->count() }}</span>
            </div>
            <div class="stat-item">
                <img src="{{ asset('img/comment-logo.png') }}" alt="コメント" class="heart-icon"> {{-- ハートと同じサイズ感を適用 --}}
                <span class="count">{{ $item->comments->count() }}</span>
            </div>
        </div>

        {{-- 購入ボタンの切り替え --}}
        @if($item->purchase)
            {{-- 売却済みの場合：グレーのボタンを表示（クリック不可） --}}
            <button class="btn-sold-out" disabled style="background-color: #888; cursor: not-allowed; width: 100%; border: none; padding: 12px; color: #fff; font-weight: bold; border-radius: 4px;">
        売り切れました</button>
        @else
            {{-- 未購入の場合：通常の購入ボタンを表示 --}}
            <a href="{{ route('purchases.create', ['item_id' => $item->id]) }}" class="btn-purchase">
                購入手続きへ
            </a>
        @endif

        {{-- 商品説明 --}}
        <div class="item-section">
            <h2 class="section-title">商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>

        {{-- 商品の情報 --}}
        <div class="item-section">
            <h2 class="section-title">商品の情報</h2>
            
            <div class="info-row">
                <span class="info-label">カテゴリー</span>
                <div class="tags">
                    @forelse($item->categories as $category)
                        <span class="tag">{{ $category->name }}</span>
                    @empty
                        <span class="tag">未設定</span>
                    @endforelse
                </div>
            </div>

            <div class="info-row">
                <span class="info-label">商品の状態</span>
                <span>{{ $item->condition->name ?? '不明' }}</span>
            </div>
        </div>

        {{-- コメントセクション --}}
        <div class="item-section">
            <h2 class="section-title">コメント ({{ $item->comments->count() }})</h2>
            
            @foreach($item->comments as $comment)
            <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 10px; font-size: 12px;">

            <div class="comment-item" style="margin-bottom: 20px;">
                <div class="comment-user">
                    <div class="user-icon-small">
                        @if($comment->user->profile && $comment->user->profile->profile_image)
                            {{-- プロフィール画像がある場合 --}}
                            <img src="{{ asset('storage/' . str_replace('\\', '/', $comment->user->profile->profile_image)) }}" 
                                alt="ユーザーアイコン" 
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            {{-- 画像がない場合のデフォルトグレー背景（既存のCSSクラスを活かす） --}}
                            <div style="width: 100%; height: 100%; background-color: #e1e1e1; border-radius: 50%;"></div>
                        @endif
                    </div>
                    <span class="comment-user-name" style="font-weight: bold;">
                        {{ $comment->user->profile->name ?? $comment->user->name }}
                    </span>
                </div>
                <div class="comment-body" style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin-top: 5px;">
                    {{ $comment->comment }}
                </div>
            </div>
            @endforeach

            {{-- コメント投稿フォーム部分 --}}
            <div class="comment-form">
                <p class="form-label-bold">商品へのコメント</p>
                
                <form action="{{ route('comment.store', $item) }}" method="POST">
                    @csrf
                    <textarea name="comment" rows="5">{{ old('comment') }}</textarea>
                    
                    @error('comment')
                        <p class="error-message">{{ $message }}</p>
                    @enderror

                    {{-- 未ログインならボタンを無効化、またはログインへ誘導 --}}
                    @auth
                        <button type="submit" class="btn-comment">コメントを送信する</button>
                    @else
                        <a href="{{ route('login') }}" class="btn-comment" style="display:block; text-align:center; text-decoration:none;">ログインしてコメントする</a>
                    @endauth
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. ボタンがクリックされた時の動きを登録
    document.getElementById('favorite-btn').addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        const heartIcon = document.getElementById('heart-icon');
        const countSpan = document.getElementById('favorite-count');

        // 2. サーバーに通信を送る
        fetch(`/item/${itemId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                // エラー時はここを通る
                return response.text().then(text => { throw new Error(text) });
            }
            return response.json();
        })
        .then(data => {
            // 3. 成功したらアイコンと数字を書き換える
            heartIcon.src = data.is_favorited 
                ? "{{ asset('img/ハートロゴ_ピンク.png') }}" 
                : "{{ asset('img/ハートロゴ_デフォルト.png') }}";
            countSpan.textContent = data.count;
        })
        .catch(error => {
            console.error('通信エラー:', error);
            alert('ログインしていないか、通信に失敗しました。');
        });
    }); // ← ここでaddEventListenerの閉じカッコ「) ;」が必要です
</script>
@endsection