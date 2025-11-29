<?php

namespace App\Repositories\Interfaces;

use App\Models\Hold;

interface HoldInterface
{
    /**
     * create a new hold
     * @param array $data
     * @return Hold
     * @throws Exception
     */
    public function store($product, $quantity);

    /**
     * get all holds
     * @return array
     * @throws Exception
     */
    public function index();

    /**
     * get hold by id
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function show(int $id);

    /**
     * get expired holds
     * @return Hold[]
     * @throws Exception
     */
    public function expired();

    /**
     * update hold by id
     * @param int $id
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function update($product, $quantity);
}
