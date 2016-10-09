<?php

namespace Zenapply\BambooHR\Api\Tests;

use Zenapply\BambooHR\Api\Tests\Mocks\BambooHRMocked;
use Zenapply\BambooHR\Api\Tests\Mocks\RequestMocked;
use Zenapply\BambooHR\Api\BambooHR;
use Zenapply\BambooHR\Api\Exceptions\BambooHRInvalidApiKeyException;

class BambooHRTest extends TestCase
{
    public function testItCreatesSuccessfully()
    {
        $r = new BambooHR("apiKey");
        $this->assertInstanceOf(BambooHR::class, $r);
    }

    public function testCall()
    {
        $v = new BambooHRMocked("apiKey");
        $resp = $v->viddler_users_auth(array('user' => "user", 'password' => "pass"));
        $this->assertInternalType('array', $resp);
    }

    public function testCallPost()
    {
        $v = new BambooHRMocked("apiKey");
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
            "203"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRUploadingDisabledException::class,
            "202"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRInvalidFormTypeException::class,
            "200"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRSizeLimitExceededException::class,
            "105"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRUsernameExistsException::class,
            "104"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRTermsNotAcceptedException::class,
            "103"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRInvalidPasswordException::class,
            "102"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRAccountSuspendedException::class,
            "101"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRForbiddenException::class,
            "100"     => \Zenapply\BambooHR\Api\Exceptions\BambooHRNotFoundException::class,
            "8"       => \Zenapply\BambooHR\Api\Exceptions\BambooHRInvalidApiKeyException::class,
            "default" => \Zenapply\BambooHR\Api\Exceptions\BambooHRException::class,
            "random"  => \Zenapply\BambooHR\Api\Exceptions\BambooHRException::class
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
