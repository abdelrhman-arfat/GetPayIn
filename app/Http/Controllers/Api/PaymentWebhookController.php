<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentWebhookRequest;
use App\Repositories\Interfaces\PaymentWebhookInterface;
use App\Traits\LoggerTrait;
use App\Utils\Response;
use Exception;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    use LoggerTrait;
    private PaymentWebhookInterface $paymentWebhookRepository;

    public function __construct(PaymentWebhookInterface $paymentWebhookRepository)
    {
        $this->paymentWebhookRepository = $paymentWebhookRepository;
    }
    public function webhook(PaymentWebhookRequest $request)
    {
        $data = $request->validated();
        $this->paymentLogging($data);

        try {
            $order = $request->getOrder();
            $paymentData = $this->paymentWebhookRepository->store($data, $order);
            return Response::success(
                $paymentData,
                "Payment processed successfully.",
                200
            );
        } catch (Exception $e) {
            $this->errorLogging($e);
            $code = $e->getCode() ?: 500;
            return Response::error(
                $e->getMessage(),
                $e->getMessage(),
                $code
            );
        }
    }
}
