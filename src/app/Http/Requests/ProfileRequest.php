<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:8'],
            'address' => ['nullable', 'string', 'max:255'],
            'building_name' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'         => 'お名前を入力してください',
            'profile_image.image'   => '指定されたファイルが画像ではありません',
            'profile_image.mimes'   => '画像の形式はjpeg, png, jpgを選択してください',
            'profile_image.max'     => '画像サイズは2MB以内でアップロードしてください',
            // 他のメッセージも必要に応じて
        ];
    }
}
