<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'COACHTECH')</title>
    
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
</head>
<body>

    <header class="header">
        <div class="header-inner">
            <div class="header-logo">
                <a href="/"><img src="{{ asset('img/coachtech_logo.png') }}" alt="COACHTECH" class="logo"></a>
            </div>

            <div class="header-center">
                {{-- ログイン・会員登録・メール認証画面「以外」の時だけ表示する --}}
                @if (!request()->routeIs('login', 'register', 'verification.notice'))
                    <form action="{{ route('item.index') }}" method="GET" class="search-form">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？" class="search-input">
                        {{-- マイリストでの検索維持 --}}
                        @if(isset($tab) && $tab === 'mylist')
                            <input type="hidden" name="tab" value="mylist">
                        @endif
                    </form>
                @endif
            </div>

            <nav class="header-right">
                <ul class="nav-list">
                    {{-- ログイン・登録・認証画面ではナビ自体を隠す、または中身を制限する --}}
                    @if (!request()->routeIs('login', 'register', 'verification.notice'))
                        @auth
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="nav-link-btn">ログアウト</button>
                                </form>
                            </li>
                            <li><a href="{{ route('profile.show') }}" class="nav-link">マイページ</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="nav-link">ログイン</a></li>
                            <li><a href="{{ route('register') }}" class="nav-link">会員登録</a></li>
                        @endauth
                        <li><a href="{{ route('item.create') }}" class="btn-sell">出品</a></li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        @if (session('message'))
        <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; text-align: center; margin: 10px auto; width: 80%; border-radius: 5px;">
            {{ session('message') }}
        </div>
    @endif
        @yield('content')
    </main>

</body>
</html>