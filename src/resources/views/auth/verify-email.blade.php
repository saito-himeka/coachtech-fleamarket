@extends('layouts.app')

@section('title', 'メール認証 | COACHTECH')

@section('content')
<div class="verify-email__container">
    <div class="verify-email__content">
        <p class="verify-email__text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        {{-- 
            本来はメール内のリンクをクリックしますが、
            「認証はこちらから」ボタンとして、メール再送ボタンをメインに配置します
        --}}
        <div class="verify-email__actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="verify-email__btn">
                    認証はこちらから
                </button>
            </form>

            <form method="POST" action="{{ route('verification.send') }}" class="verify-email__resend-form">
                @csrf
                <button type="submit" class="verify-email__resend-link">
                    認証メールを再送する
                </button>
            </form>
        </div>

        {{-- 再送完了時のメッセージ表示 --}}
        @if (session('status') == 'verification-link-sent')
            <p class="verify-email__success">
                新しい認証リンクを送信しました！
            </p>
        @endif
    </div>
</div>
@endsection