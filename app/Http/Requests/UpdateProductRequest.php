<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'slug' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'quantity' => 'required|integer',
            'discount' => 'nullable|integer|between:0,100',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'=>false,
            'message'=>'failed',
            'data'=>$validator->errors()
        ]));
    }
}
