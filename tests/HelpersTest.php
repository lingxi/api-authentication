<?php

use Lingxi\Signature\Helpers;

class HelpersTest extends PHPUnit_Framework_TestCase
{
    public function testIsJson()
    {
        $json = json_encode(['foo' => 'bar']);

        $this->assertTrue(Helpers::isJson($json));
    }

    public function testCreateLinkstringUrlencode()
    {
        $data = [
            'name' => '灵析'
        ];

        $this->assertEquals(Helpers::createLinkstringUrlencode($data), http_build_query($data));
    }
}
