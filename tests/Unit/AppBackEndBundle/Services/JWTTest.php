<?php
/**
 * Created by PhpStorm.
 * User: imartynyuk
 * Date: 15.12.15
 * Time: 12:54
 */

namespace Tests\Functional\AppBackEndBundle\Services;

use AppBackEndBundle\Services\JWT;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;

class JWTTest extends WebTestCase
{
    protected $object;

    public function setUp()
    {
        $this->object = new JWT('secret');
    }

    /**
     * @dataProvider getDataProvider
     */
    public function testGetData($expected, $token, $allowedAlgs)
    {
        $decoded = $this
            ->object
            ->getData($token, $allowedAlgs);

        $this->assertEquals($expected, $decoded);
    }

    public function generateToken($data, $expirationTime, $alg)
    {
        $jwtService = new JWT('secret');

        return $jwtService->generate($data, $expirationTime, $alg);
    }

    public function getDataProvider()
    {
        $testData = (object)[
            'test1' => '1'
        ];

        return [
            [
                $testData,
                $this->generateToken($testData, 999999, 'HS256'),
                'HS256'
            ], [
                false,
                $this->generateToken($testData, 0, 'HS256'),
                'HS256'
            ]
        ];
    }
}
