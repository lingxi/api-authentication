<?php

use Lingxi\Signature\Checker\VersionChecker;
use Lingxi\Signature\Checker\TimestampChecker;

class VersionCheckerTest extends PHPUnit_Framework_TestCase
{
    public function test_pass_when_two_version_equal()
    {
        $this->assertTrue(VersionChecker::check('vc2', 'vc2'));
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\ApiVersionException
     */
    public function test_it_throw_api_version_exception_once()
    {
        VersionChecker::check('v1', 'v2');
    }
}
