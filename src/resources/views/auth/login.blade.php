@extends('layouts.app')

@section('title', 'ログイン - COACHTECH')

@section('content')
<div class="auth-container">
    <h1 class="page-title">ログイン</h1>

    {{-- ブラウザの吹き出しを防ぐために novalidate を追加 --}}
    <form action="{{ route('login') }}" method="POST" novalidate>
        @csrf
        
        {{-- メールアドレス --}}
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" name="email" id="email" 
                class="form-input @error('email') input-error @enderror" 
                value="{{ old('email') }}">
            
            {{-- 指定の文言「メールアドレスを入力してください」などが表示される --}}
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" name="password" id="password" 
                class="form-input @error('password') input-error @enderror">
            
            {{-- 指定の文言「パスワードを入力してください」が表示される --}}
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-submit">ログインする</button>
    </form>

    <div class="footer-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</div>
@endsection