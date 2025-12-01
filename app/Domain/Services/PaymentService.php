<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\PaymentInterface;

class PaymentService implements PaymentInterface
{

    public function trackPayment($tackId, $orderId, $amount, $fakeStatus = "success")
    {
        // TODO:Change fake data to real payment provider for tracking your payment status 
    }
}
