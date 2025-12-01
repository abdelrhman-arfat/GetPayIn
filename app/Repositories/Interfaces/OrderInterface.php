<?php

namespace App\Repositories\Interfaces;

interface OrderInterface
{
    /**
     * create new order
     * @param Hold $hold
     * @return Order
     * @throws \Exception
     */
    public function store($hold);


    /**
     * @return Order
     * @throws Exception
     */
    public function show($id);
}
