<?php

namespace AppBackEndBundle\Services;

use Firebase\JWT\JWT as Base;

class JWT extends Base
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
            'data' => $data,
            'iat' => time(),
            'exp' => time() + $this->expirationTime
        ];

        return self::encode($data, $this->secret, $alg);
    }

    public function getData($jwt, $allowed_algs = array('HS256'))
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

        return $data->data;
    }
}
