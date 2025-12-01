<?php

namespace App\Domain\Interfaces;

interface PaymentInterface
{
    /**
     * track payment
     * @param string $tackId
     * @return array
     */
    public function trackPayment($tackId, $orderId, $amount, $fakeStatus);
}
