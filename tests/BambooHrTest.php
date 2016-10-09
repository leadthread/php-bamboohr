<?php

namespace Zenapply\BambooHr\Api\Tests;

use Zenapply\BambooHr\Api\Tests\Mocks\BambooHrMocked;
use Zenapply\BambooHr\Api\Tests\Mocks\RequestMocked;
use Zenapply\BambooHr\Api\BambooHr;
use Zenapply\BambooHr\Api\Exceptions\BambooHrInvalidApiKeyException;

class BambooHrTest extends TestCase
{
    public function testItCreatesSuccessfully()
    {
        $r = new BambooHr("apiKey");
        $this->assertInstanceOf(BambooHr::class, $r);
    }

    public function testCall()
    {
        $v = new BambooHrMocked("apiKey");
        $resp = $v->viddler_users_auth(array('user' => "user", 'password' => "pass"));
        $this->assertInternalType('array', $resp);
    }

    public function testCallPost()
    {
        $v = new BambooHrMocked("apiKey");
        $resp = $v->viddler_encoding_cancel(array('user' => "user", 'password' => "pass"));
        $this->assertInternalType('array', $resp);
    }

    public function testBinaryArgs()
    {
        $v = new RequestMocked("apiKey", "viddler.videos.setThumbnail", [[
            'file' => 'file',
            'random' => 'value'
        ]]);
        $args = $v->getBinaryArgs();
        $this->assertInternalType('array', $args);
    }

    public function testValidResponse()
    {
        $valid = [
            "viddler_api" => [
                "version" => "3.9.0"
            ]
        ];
        $v = new RequestMocked("apiKey", "method", []);
        $response = $v->checkResponseForErrors($valid);

        $this->assertEquals($valid, $response);
    }

    public function testExceptions()
    {
        $exceptions = [
            "203"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrUploadingDisabledException::class,
            "202"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrInvalidFormTypeException::class,
            "200"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrSizeLimitExceededException::class,
            "105"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrUsernameExistsException::class,
            "104"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrTermsNotAcceptedException::class,
            "103"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrInvalidPasswordException::class,
            "102"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrAccountSuspendedException::class,
            "101"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrForbiddenException::class,
            "100"     => \Zenapply\BambooHr\Api\Exceptions\BambooHrNotFoundException::class,
            "8"       => \Zenapply\BambooHr\Api\Exceptions\BambooHrInvalidApiKeyException::class,
            "default" => \Zenapply\BambooHr\Api\Exceptions\BambooHrException::class,
            "random"  => \Zenapply\BambooHr\Api\Exceptions\BambooHrException::class
        ];

        $v = new RequestMocked("apiKey", "method", []);
        foreach ($exceptions as $code => $exception) {
            try {
                $v->checkResponseForErrors([
                    "error" => ["code" => $code]
                ]);
                $this->fail('No exception thrown');
            } catch (\Exception $e) {
                $this->assertInstanceOf($exception, $e);
            }
        }
    }
}
