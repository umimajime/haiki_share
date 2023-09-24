<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEditItem extends FormRequest
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
      'item_id' => 'required|integer',
      'image' => 'nullable|file|mimes:jpg,jpeg,png,gif',
      'name' => 'required|string|max:255',
      'price' => 'required|integer|digits_between:1,10',
      'expiry_date' => 'required|date_format:Y-m-d',
      'imageChangeFlg' => 'required|numeric',
    ];
  }
}
