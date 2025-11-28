<?php

namespace App\Repositories\Interfaces;

interface UserInterface
{
    /**
     * register a new user in database
     * @name create
     * @param array $data
     * @return array
     * @throws DuplicateEntry if email already exits
     * 
     **/
    public function register(array $data);

    /**
     * login a user
     * @name login
     * @param array $data
     * @return array
     * @throws Unauthorized if user credentials isn't right
     * 
     **/
    public function login(array $data);
}
