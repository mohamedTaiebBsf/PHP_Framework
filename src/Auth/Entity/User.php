<?php

namespace App\Auth\Entity;

use Framework\Auth\User as UserAuth;

class User implements UserAuth
{
    public $id;
    public $username;
    public $email;
    public $password;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return [];
    }
}
