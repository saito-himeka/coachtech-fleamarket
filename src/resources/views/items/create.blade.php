@extends('layouts.app')

@section('content')
<div class="sell-container">
    <h1 class="page-title">商品の出品</h1>

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像アップロード部分 --}}
        <div class="form-group">
            <p class="form-label-bold">商品画像</p>
            <div class="upload-area">
                {{-- プレビュー表示エリアを追加 --}}
                <div id="image-preview-container" style="margin-bottom: 15px; display: none;">
                    <img id="preview" src="" alt="プレビュー" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                </div>

                <label class="upload-btn">
                    画像を選択する
                    <input type="file" name="item_image" id="item_image" style="display:none;" accept="image/*">
                </label>
            </div>
        </div>

        {{-- 商品の詳細 --}}
        <section class="sell-section">
            <h2 class="sell-section-title">商品の詳細</h2>
            
            <div class="form-group">
                <p class="form-label-bold">カテゴリー</p>
                <div class="category-group">
                    @foreach($categories as $category)
                        <label class="category-label">
                            {{-- value を $category->id に変更 --}}
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                            <span class="category-tag">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label for="condition" class="form-label-bold">商品の状態</label>
                <div class="select-wrapper">
                    <select name="condition_id" id="condition" class="form-select">
                        <option value="" disabled selected>選択してください</option>
                        
                        {{-- ここを foreach に変更します --}}
                        @foreach($conditions as $condition)
                            <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- 商品名と説明 --}}
        <section class="sell-section">
            <h2 class="sell-section-title">商品名と説明</h2>
            
            <div class="form-group">
                <label for="name" class="form-label-bold">商品名</label>
                <input type="text" name="name" id="name" class="form-input">
            </div>

            <div class="form-group">
                <label for="brand" class="form-label-bold">ブランド名</label>
                <input type="text" name="brand" id="brand" class="form-input">
            </div>

            <div class="form-group">
                <label for="description" class="form-label-bold">商品の説明</label>
                <textarea name="description" id="description" rows="6" class="form-textarea"></textarea>
            </div>
        </section>

        {{-- 販売価格 --}}
        <section class="sell-section">
            <h2 class="sell-section-title">販売価格</h2>
            <div class="form-group">
                <label for="price" class="form-label-bold">販売価格</label>
                <div class="price-input-wrapper">
                    <span class="price-symbol">¥</span>
                    <input type="number" name="price" id="price" class="form-input-price">
                </div>
            </div>
        </section>

        <button type="submit" class="btn-submit-red">出品する</button>
    </form>
</div>

<script>
    document.getElementById('item_image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('image-preview-container');
        const previewImage = document.getElementById('preview');

        if (file) {
            const reader = new FileReader();

            // ファイルを読み込み終わったら実行
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block'; // 非表示だった枠を表示
            }

            reader.readAsDataURL(file); // 画像をデータURLとして読み込む
        }
    });
</script>
@endsection