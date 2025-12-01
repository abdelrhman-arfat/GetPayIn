<?php

namespace App\Http\Requests;

use App\Rules\ProductRule;
use App\Utils\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreHoldRequest extends FormRequest
{
    private ProductRule $productRule;

    public function __construct(ProductRule $productRule)
    {
        parent::__construct();
        $this->productRule = $productRule;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "product_id" => ["required", $this->productRule],
            "qty" => ["required", "numeric", "min:1"],
        ];
    }

    public function messages()
    {
        return [
            "qty.required" => "The quantity field is required.",
            "qty.min" => "The quantity field must be at least 1."
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error($validator->errors()->first(), $validator->errors()->first(), 422));
    }


    public function getProduct()
    {
        return $this->productRule->getProduct();
    }
}
