<?php

namespace App\Repositories\Interfaces;

interface PaymentWebhookInterface
{
    public function store($data, $order);
}
