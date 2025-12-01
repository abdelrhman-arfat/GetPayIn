<?php

namespace App\Http\Requests;

use App\Rules\OrderRule;
use App\Utils\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentWebhookRequest extends FormRequest
{

    private OrderRule $orderRule;

    public function __construct(OrderRule $orderRule)
    {
        $this->orderRule = $orderRule;
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
            "order_id" => ["required", $this->orderRule],
            'status' => 'required|in:success,canceled,failed',
            "idempotency_key" => ["required", 'unique:payment_webhooks,idempotency_key']

        ];
    }
    public function validationData()
    {
        return array_merge($this->all(), [
            "idempotency_key" => $this->header('Idempotency-Key') ?? $this->input('idempotency_key')
        ]);
    }



    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error($validator->errors()->first(), $validator->errors()->first(), 422));
    }

    public function getOrder()
    {
        return $this->orderRule->getOrder();
    }
}
