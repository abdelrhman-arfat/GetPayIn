<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ProductInterface;
use App\Traits\LoggerTrait;
use App\Utils\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use LoggerTrait;
    private ProductInterface $productRepository;

    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        try {
            return $this->productRepository->index();
        } catch (Exception $e) {
            $this->errorLogging($e);
            return Response::error("Error happen try again latter", $e->getMessage(), $e->getCode());
        }
    }

    public function show(int $id)
    {
        try {
            $product = $this->productRepository->show($id);
            return Response::success($product);
        } catch (ModelNotFoundException $e) {
            return Response::error("Product not found", $e->getMessage(), 404);
        } catch (Exception $e) {
            $this->errorLogging($e);
            return Response::error("Error happen try again latter", $e->getMessage(), $e->getCode());
        }
    }
}
