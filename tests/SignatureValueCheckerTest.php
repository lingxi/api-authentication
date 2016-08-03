<?php

use Lingxi\Signature\Checker\SignatureValueChecker;

class SignatureValueCheckerTest extends PHPUnit_Framework_TestCase
{
    public function test_it_pass_when_request_value_equal_value()
    {
        $this->assertTrue(SignatureValueChecker::check(12, 12));
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureValueException
     */
    public function test_it_throw_signature_value_exception()
    {
        SignatureValueChecker::check(12, 21);
    }
}
