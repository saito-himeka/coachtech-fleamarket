@extends('layouts.app')

@section('title', '購入手続き - ' . $item->name)

@section('content')
<div class="purchase-container">
    {{-- 左側：メイン入力エリア --}}
    <div class="purchase-main">
        {{-- 商品概要 --}}
        <div class="purchase-item-info">
            <div class="purchase-item-image">
                @if($item->image_paths && count($item->image_paths) > 0)
                    <img src="{{ $item->image_paths[0] }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    No Image
                @endif
            </div>
            <div class="purchase-item-text">
                <h1 class="purchase-item-name">{{ $item->name }}</h1>
                <p class="purchase-item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        {{-- 支払い方法セクション --}}
        <div class="purchase-section">
            <h2 class="purchase-section-title">支払い方法</h2>
            <select name="payment_method" class="payment-select">
                <option value="" disabled selected>選択してください</option>
                <option value="konbini">コンビニ払い</option>
                <option value="card">クレジットカード</option>
            </select>
        </div>

        {{-- 配送先セクション --}}
        <div class="purchase-section">
            <div class="section-header">
                <h2 class="purchase-section-title">配送先</h2>
                <a href="{{ route('purchases.address.edit', ['item_id' => $item->id]) }}" class="change-link">
                    変更する
                </a>
            </div>
            <div class="address-display">
                {{-- Auth::user()->profile が存在する場合のみ表示 --}}
                @if(Auth::user()->profile)
                    <p>〒 {{ Auth::user()->profile->post_code }}</p>
                    <p>{{ Auth::user()->profile->address }}</p>
                    <p>{{ Auth::user()->profile->building_name }}</p>
                @else
                    <p>〒 000-0000</p>
                    <p>住所が登録されていません</p>
                @endif
            </div>
        </div>
    </div>

    {{-- 右側：確認・確定エリア --}}
    <div class="purchase-side">
        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <th>商品代金</th>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
                <tr>
                    <th>支払い金額</th>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td id="selected-payment">未選択</td>
                </tr>
            </table>
        </div>
        <form action="{{ route('purchases.store', ['item_id' => $item->id]) }}" method="POST">
            @csrf
            {{-- 実際にはJSで選択された支払い方法を隠しフィールドに入れる等の処理が必要 --}}
            <input type="hidden" name="payment_method" id="payment-method-hidden">
            <button type="submit" class="btn-purchase-confirm">購入する</button>
        </form>
    </div>
</div>

<script>
    // 支払い方法を選択した時に、右側のテーブルに反映させる簡単なJS
    document.querySelector('.payment-select').addEventListener('change', function() {
        const selectedText = this.options[this.selectedIndex].text;
        document.getElementById('selected-payment').textContent = selectedText;
        document.getElementById('payment-method-hidden').value = this.value;
    });
</script>
@endsection