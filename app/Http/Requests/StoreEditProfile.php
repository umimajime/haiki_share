<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEditProfile extends FormRequest
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
            'store_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'post_code' => 'nullable|string|regex:{^\d{3}-\d{4}$}',
            'prefecture' => 'nullable|string',
            'municipality' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'email' => 'nullable|string|email:filter,dns|max:255|unique:users|unique:stores',
            'password' => 'nullable|string|min:8|max:24|regex:{^[a-zA-Z0-9.?/-]{8,24}$}|confirmed',
        ];
    }
}
