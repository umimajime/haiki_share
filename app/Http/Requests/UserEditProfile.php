<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEditProfile extends FormRequest
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
            'email' => 'nullable|string|email:filter,dns|max:255|unique:users|unique:stores',
            'password' => 'nullable|string|min:8|max:24|regex:{^[a-zA-Z0-9.?/-]{8,24}$}|confirmed',
        ];
    }
}
