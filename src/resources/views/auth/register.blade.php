@extends('layouts.app')

@section('title', '会員登録 - COACHTECH')

@section('content')
<div class="auth-container">
    <h1 class="page-title">会員登録</h1>

    <form action="{{ route('register') }}" method="POST" novalidate>
        @csrf
        
        {{-- ユーザー名 --}}
        <div class="form-group">
            <label for="name" class="form-label">ユーザー名</label> 
            <input type="text" name="name" id="name" 
                class="form-input @error('name') input-error @enderror" 
                value="{{ old('name') }}">
            
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- メールアドレス --}}
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" name="email" id="email" 
                class="form-input @error('email') input-error @enderror" 
                value="{{ old('email') }}">
            
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" name="password" id="password" {{-- idを追加 --}}
                class="form-input @error('password') input-error @enderror">
            
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 確認用パスワード --}}
        <div class="form-group">
            <label for="password_confirmation" class="form-label">確認用パスワード</label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                class="form-input">
        </div>

        <button type="submit" class="btn-submit">登録する</button>
    </form>

    <div class="footer-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
</div>
@endsection