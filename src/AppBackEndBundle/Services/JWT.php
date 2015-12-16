<?php

namespace AppBackEndBundle\Services;

use Firebase\JWT\JWT as Base;

class JWT extends Base
{
    protected $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function generate($data, $expirationTime = 60, $alg = 'HS256')
    {
        $data = [
            'data' => $data,
            'iat' => time(),
            'exp' => time() + $expirationTime
        ];

        return self::encode($data, $this->secret, $alg);
    }

    public function getData($jwt, $allowed_algs = array('HS256'))
    {
        if (!is_array($allowed_algs)) {
            $allowed_algs = (array)$allowed_algs;
        }

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
