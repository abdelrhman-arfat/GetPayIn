<?php

namespace App\Http\Requests;

use App\Rules\HoldRule;
use App\Utils\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
    private HoldRule $holdRule;
    public function __construct(HoldRule $holdRule)
    {
        parent::__construct();
        $this->holdRule = $holdRule;
    }
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
            "hold_id" => ["required", $this->holdRule],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error($validator->errors()->first(), $validator->errors()->first(), 422));
    }

    public function getHold()
    {
        return $this->holdRule->getHold();
    }
}
