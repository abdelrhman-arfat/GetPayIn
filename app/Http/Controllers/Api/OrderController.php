<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Repositories\Interfaces\OrderInterface;
use App\Traits\LoggerTrait;
use App\Utils\Response;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use LoggerTrait;

    private OrderInterface $orderRepository;

    public function __construct(OrderInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $hold = $request->getHold();
            $order  = $this->orderRepository->store($hold);
            return Response::success($order, 'your order has been booked', 201);
        } catch (\Exception $e) {
            $this->errorLogging($e);
            return Response::error($e->getMessage(), $e->getMessage(), $e->getCode());
        }
    }
}
