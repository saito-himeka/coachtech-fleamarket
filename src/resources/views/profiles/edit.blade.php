@extends('layouts.app')

@section('content')
<div class="profile-container">
    <h2 class="page-title">プロフィール設定</h2>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
        @csrf
        @method('PUT')

        {{-- プロフィール画像セクション --}}
        <div class="image-section">
            <div class="image-preview" id="image-preview-wrapper">
                @if(Auth::user()->profile && Auth::user()->profile->profile_image)
                    {{-- id="profile-img-output" を追加 --}}
                    <img src="{{ asset('storage/' . Auth::user()->profile->profile_image) }}" alt="ユーザーアイコン" id="profile-img-output">
                @else
                    {{-- 画像がない時にプレビューを表示するための img タグ（最初は非表示） --}}
                    <div class="default-circle" id="default-circle"></div>
                    <img src="" alt="ユーザーアイコン" id="profile-img-output" style="display: none;">
                @endif
            </div>
            <label class="image-select-label">
                画像を選択する
                {{-- id="profile_image_input" を追加 --}}
                <input type="file" name="profile_image" id="profile_image_input" style="display: none;" accept="image/*">
            </label>
        </div>
        @error('profile_image')
            <div class="error-message" style="color: #ff4d4b; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
        @enderror    

        {{-- 各入力項目 --}}
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}">
            @error('name')
                <div class="error-message" style="color: #ff4d4b; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="post_code">郵便番号</label>
            <input type="text" name="post_code" id="post_code" value="{{ old('post_code', Auth::user()->profile->post_code ?? '') }}">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', Auth::user()->profile->address ?? '') }}">
        </div>

        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" id="building_name" value="{{ old('building_name', Auth::user()->profile->building_name ?? '') }}">
        </div>

        <button type="submit" class="update-btn">更新する</button>
    </form>
</div>
<script>
    document.getElementById('profile_image_input').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const output = document.getElementById('profile-img-output');
        const defaultCircle = document.getElementById('default-circle');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                // 画像のソースを選んだファイルに差し替え
                output.src = e.target.result;
                output.style.display = 'block'; // 画像を表示
                
                // もしデフォルトのグレー円があれば隠す
                if (defaultCircle) {
                    defaultCircle.style.display = 'none';
                }
            }

            reader.readAsDataURL(file);
        }
    });
</script>
@endsection