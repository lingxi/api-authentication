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

        $this->assertEquals(Helpers::createLinkstring($data), 'name=灵析');

        $data = [
            'name' => '灵析',
            'bool' => false,
            'int' => 100,
        ];

        $this->assertEquals(Helpers::createLinkstring($data), 'name=灵析&bool=0&int=100');
    }
}
