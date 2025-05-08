<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface{
    public function updateActivedUser($id);
    public function checkEmail(string $email);
    public function checkEmailWithId(string $email, int $id);

}