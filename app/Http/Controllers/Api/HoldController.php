<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Forbidden;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHoldRequest;
use App\Http\Resources\HoldResource;
use App\Repositories\Interfaces\HoldInterface;
use App\Repositories\Interfaces\ProductInterface;
use App\Traits\LoggerTrait;
use App\Utils\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoldController extends Controller
{

    use LoggerTrait;

    private ProductInterface $productRepository;
    private HoldInterface $holdRepository;
    public function __construct(ProductInterface $productRepository, HoldInterface $holdRepository)
    {
        $this->holdRepository = $holdRepository;
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {
        return [];
    }


    public function store(StoreHoldRequest $request)
    {
        try {
            $data = $request->validated();
            $product = $request->getProduct();
            $quantity = $data['qty'];
            $hold = $this->holdRepository->store($product, $quantity);
            $resData = new HoldResource($hold);
            return Response::success($resData, 'your hold has been created', 201);
        } catch (ModelNotFoundException $e) {
            return Response::error("product not found", $e->getMessage(), 404);
        } catch (Exception $e) {
            $this->errorLogging($e);
            return Response::error($e->getMessage(), $e->getMessage(), $e->getCode());
        }
    }
}
