<?php

namespace App;

class Auth
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $password;

    /**
     * Auth constructor.
     * @param $username
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getBasicHeaderArray()
    {
        if ($this->password != null) {
            return [$this->username, $this->password];
        } else {
            return [$this->username, $this->token];
        }
    }
}
