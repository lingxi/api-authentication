<?php

use Lingxi\Signature\Authenticator;

class AuthenticatorTest extends PHPUnit_Framework_TestCase
{
    protected $data;

    protected $authenticator;

    protected $api_secret;

    protected $signature;

    public function __construct()
    {
        $this->data = [
            'cid'       => 12,
            'stamp'     => time(),
            'noncestr'  => 'thisisademode',
            'app_key'   => '121212'
        ];

        $this->authenticator = new Authenticator;
    }

    public function test_it_can_pass_all()
    {
        $this->setSecret('abc');
        $this->generateSignature();

        $this->data['signature'] = $this->signature;
        $this->data['version'] = 'v1';
        $this->authenticator->api_secret = $this->api_secret;

        $this->assertTrue($this->authenticator->attempt($this->data));
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\ApiVersionException
     */
    public function test_it_throw_version_exception()
    {
        $this->setSecret('abc');
        $this->generateSignature();

        $this->data['signature'] = $this->signature;
        $this->data['version'] = 'v2';

        $this->authenticator->api_secret = $this->api_secret;

        $this->authenticator->attempt($this->data);
    }

    /**
     * @expectedException Lingxi\Signature\Exceptions\SignatureKeyException
     */
    public function test_it_throw_signature_key_not_exist_exception()
    {
        $this->setSecret('abc');
        $this->generateSignature();

        $this->data['signature'] = $this->signature;
        $this->data['version'] = 'v1';

        $this->authenticator->attempt($this->data);
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureTimestampException
     */
    public function test_it_throw_timestamp_exception()
    {
        $this->setSecret('abc');
        $this->generateSignature();

        $this->data['signature'] = $this->signature;
        $this->data['version'] = 'v1';
        $this->data['stamp'] = time() - 301;

        $this->authenticator->api_secret = $this->api_secret;

        $this->authenticator->attempt($this->data);
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureValueException
     */
    public function test_it_throw_signature_value_exception()
    {
        $this->setSecret('abc');
        $this->generateSignature();

        $this->data['signature'] = $this->signature;
        $this->data['version'] = 'v1';

        $this->authenticator->api_secret = $this->api_secret . '1212';

        $this->authenticator->attempt($this->data);
    }

    public function setSecret($secret)
    {
        $this->api_secret = $secret;
    }

    public function generateSignature()
    {
        natsort($this->data);

        $this->signature = hash_hmac("sha256", http_build_query($this->data), $this->api_secret);;
    }
}