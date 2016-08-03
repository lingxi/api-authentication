<?php

use Lingxi\Signature\Checker\SignatureKeyChecker;

class SignatureKeyCheckerTest extends PHPUnit_Framework_TestCase
{
    protected $secretsFromDb;

    public function __construct()
    {
        $this->secretsFromDb = [null, '', 0];
    }

    public function test_it_pass_when_has_secret()
    {
        $this->assertTrue(SignatureKeyChecker::check(12));
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureKeyException
     */
    public function test_it_throw_signature_key_exception()
    {
        while (list($secret) = $this->secretsFromDb) {
            SignatureKeyChecker::check($secret);
        }
    }
}
