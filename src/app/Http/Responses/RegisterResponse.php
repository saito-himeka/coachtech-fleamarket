<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * 会員登録後のレスポンス（リダイレクト先）をカスタマイズ
     */
    public function toResponse($request)
    {
        // 会員登録直後は、認証待ち画面（verify-email）へリダイレクトさせる
        return redirect()->route('verification.notice');
    }
}