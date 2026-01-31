<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    public function toResponse($request)
    {
        // メール認証成功時のみ、プロフィール編集画面へリダイレクト
        return redirect()->route('profile.edit')->with('message', 'メール認証が完了しました。プロフィールの設定をお願いします。');
    }
}