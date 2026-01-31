@extends('layouts.app')

@section('content')
<div class="auth-container">
    <h1 class="page-title">住所の変更</h1>

    <form action="{{ route('purchases.address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="post_code" class="form-label">郵便番号</label>
            <input type="text" name="post_code" id="post_code" class="form-input" value="{{ old('post_code', $user->profile->post_code ?? '') }}">
        </div>

        <div class="form-group">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $user->profile->address ?? '') }}">
        </div>

        <div class="form-group">
            <label for="building_name" class="form-label">建物名</label>
            <input type="text" name="building_name" id="building_name" class="form-input" value="{{ old('building_name', $user->profile->building_name ?? '') }}">
        </div>

        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>
@endsection