<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class LdapUser implements Authenticatable
{
    public $username;
    public $name;
    public $email;
    public $groups = [];

    public function __construct($username, $name, $email, $groups)
    {
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->groups = $groups;
    }

    // Métodos que Laravel necesita para Auth
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getAuthPassword()
    {
        return null; // no usamos password local
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // no usamos remember_token
    }

    public function getRememberTokenName()
    {
        return null;
    }

    public function hasGroup($group)
    {
        return in_array($group, $this->groups);
    }
}
