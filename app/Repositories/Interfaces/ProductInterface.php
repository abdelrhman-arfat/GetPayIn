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
     * update product by id
     * @param int $id
     * @param array $data
     * @return object
     */
    public function update(int $id, $data = null);

    /**
     * get product by id
     * @param int $id
     * @return object
     * @throws Exception
     **/
    public function show(int $id): object;

    /**
     * check product quantity
     * @param int|object $product
     * @param int $quantity
     * @return bool
     */
    public function hasQuantity(int|object $product, int $quantity): bool;

    /**
     * update quantity product by id
     * @param int $id
     * @return void
     */
    public function updateQuantity(int|object $product, int $quantity);
}
