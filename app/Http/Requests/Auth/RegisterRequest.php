<?php

namespace App\Http\Requests\Auth;

use App\Utils\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            "name" => "required|string|max:255|min:2",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
        ];
    }

    /**
     * When validation fails, redirect the user back to the form with their input.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error($validator->errors()->first(), $validator->errors(), 422));
    }
}
