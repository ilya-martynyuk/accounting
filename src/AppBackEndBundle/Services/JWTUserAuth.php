<?php

namespace AppBackEndBundle\Services;

use Firebase\JWT\JWT as Base;

class JWTUserAuth extends Base
{
    protected $secret;
    protected $expirationTime;

    public function __construct($secret, $expirationTime)
    {
        $this->secret = $secret;
        $this->expirationTime = $expirationTime;
    }

    public function generate($data, $alg = 'HS256')
    {
        $data = [
            'user' => [
                'id' => $data->getId(),
                'username' => $data->getUsername(),
                'email' => $data->getEmail()
            ],
            'iat' => time(),
            'exp' => time() + $this->expirationTime
        ];

        return self::encode($data, $this->secret, $alg);
    }

    public function getUserData($jwt, $allowed_algs = array('HS256'))
    {
        try {
            $data = self::decode($jwt, $this->secret, $allowed_algs);
        } catch (\InvalidArgumentException $e) {
            return false;
        } catch (\UnexpectedValueException $e) {
            return false;
        } catch (\DomainException $e) {
            return false;
        }

        if ($data->exp <= time()) {
            return false;
        }

        return $data->user;
    }
}
