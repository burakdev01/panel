<?php

namespace App\Libraries;

class Auth
{
    public $id;
    public $username;
    public $loggedIn;

    public function __construct()
    {
        $this->id       = session()->get('id');
        $this->username = session()->get('username');
        $this->loggedIn = session()->get('isLoggedIn');
    }

    public function check()
    {
        return $this->loggedIn === true;
    }
}