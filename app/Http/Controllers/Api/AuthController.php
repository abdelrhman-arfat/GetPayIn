<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\Interfaces\UserInterface;
use App\Traits\LoggerTrait;
use App\Utils\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    use LoggerTrait;
    private UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->userRepository->register($data);
            return Response::success($user);
        } catch (Exception $e) {
            $this->errorLogging($e);
            return Response::error($e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $data = $request->all();
            $user = $this->userRepository->login($data);
            return Response::success($user);
        } catch (ModelNotFoundException $e) {
            return Response::error("User Credentials isn't right", $e->getMessage(), 422);
        } catch (Exception $e) {
            $this->errorLogging($e);
            return Response::error($e->getMessage(), null, $e->getCode());
        }
    }
}
