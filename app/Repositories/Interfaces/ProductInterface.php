<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;

interface ProductInterface
{
    /**
     * get all products
     * @return array
     * @throws Exception
     **/
    public function index();

    /**
     * get product by id
     * @param int $id
     * @return array
     * @throws Exception
     **/
    public function show(int $id);
}
