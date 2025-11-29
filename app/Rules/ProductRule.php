<?php

namespace App\Rules;

use App\Repositories\Interfaces\ProductInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductRule implements ValidationRule
{
    private ProductInterface $productRepository;

    private $product;

    public function __construct(ProductInterface $pI)
    {
        $this->productRepository = $pI;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $this->product = $this->productRepository->show($value);
        $quantity = request()->input('quantity');

        if (!$this->product) {
            $fail("Product not found");
            return;
        }

        if ($this->product->stock < $quantity) {
            $fail("Product quantity is not enough");
        }
    }


    public function getProduct()
    {
        return $this->product;
    }
}
