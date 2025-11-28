<?php

namespace App\Repositories\Services;

use App\Exceptions\DuplicateEntry;
use App\Exceptions\Unauthorized;
use App\Models\User;
use App\Repositories\Interfaces\UserInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService implements UserInterface
{
    public function register($data)
    {
        $isExits = User::where("email", $data["email"])->first();
        if ($isExits) {
            throw new DuplicateEntry("Email already exits" );
        }
        $data["password"] = Hash::make($data["password"]);
        $user =  User::create($data);
        $token = $user->createToken("api.token")->plainTextToken;
        return array_merge(["user" => $user], ["token" => $token]);
    }

    public function login($data)
    {
        $user = User::where("email", $data["email"])->firstOrFail();

        if (!Hash::check($data["password"], $user->password)) {
            throw new Exception("Credentials isn't right", 401);
        }

        $token = $user->createToken("api.token")->plainTextToken;
        return array_merge(["user" => $user], ["token" => $token]);
    }
}
