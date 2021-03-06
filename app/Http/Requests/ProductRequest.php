<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $id = $this->segment(4);
        return [
            "name"          => "required|unique:products,name,{$id},id",
            "description"   => "max:1000",
            "category_id"   => "required|exists:categories,id",
            "image"         => "image"
        ];
    }
}
