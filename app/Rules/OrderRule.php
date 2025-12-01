<?php

namespace App\Rules;

use App\Repositories\Interfaces\OrderInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderRule implements ValidationRule
{

    private OrderInterface $orderRepository;
    private $order;

    public function __construct(OrderInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    /**
     * Run the validation rule.
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $this->order = $this->orderRepository->show($value);
        if (!$this->order) {
            $fail("Order not found");
            return;
        }
        
        if (!$this->order->isAvailable()) {
            $status = ucfirst($this->order->status);
            $fail("This order cannot be used. Current status: {$status}.");
            return;
        }
    }


    public function getOrder()
    {
        return $this->order ?? null;
    }
}
