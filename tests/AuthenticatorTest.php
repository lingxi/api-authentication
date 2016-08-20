<?php

use Lingxi\Signature\Authenticator;

class AuthenticatorTest extends PHPUnit_Framework_TestCase
{
    protected $data;

    protected $authenticator;

    protected $api_key = '12';
    protected $api_secret = '21';

    protected $signature;

    public function __construct()
    {
        $this->data = [
            'cid'       => 12,
            'Stamp'     => time(),
            'noncestr'  => 'thisisademode',
            'api_key'   => $this->api_key
        ];

        $this->authenticator = new Authenticator($this->api_key, $this->api_secret);
    }

    public function test_it_can_pass_all()
    {
        $this->generateSignature();

        $this->data['signature'] = $this->signature;
        $this->authenticator->api_secret = $this->api_secret;

        $this->assertTrue($this->authenticator->attempt($this->data));
    }

    public function test_verify_work_well()
    {
        $parameters = $this->authenticator->getAuthParams([]);

        $this->assertTrue($this->authenticator->verify($parameters));
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureValueException
     */
    public function test_it_throw_signature_value_exception_when_secret_not_match()
    {
        $this->generateSignature();

        $this->data['signature'] = $this->signature;

        $this->authenticator->api_secret = $this->api_secret . '1212';

        $this->authenticator->attempt($this->data);
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureValueException
     */
    public function test_it_throw_signature_value_exception_when_add_parameter()
    {
        $this->generateSignature();

        $this->data['extra'] = 'some string';
        $this->data['signature'] = $this->signature;

        $this->authenticator->api_secret = $this->api_secret;

        $this->authenticator->attempt($this->data);
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureValueException
     */
    public function test_it_throw_signature_value_exception_when_signature_not_match()
    {
        $this->generateSignature();

        $this->data['signature'] = $this->signature . '!';

        $this->authenticator->api_secret = $this->api_secret;

        $this->authenticator->attempt($this->data);
    }

    public function test_it_can_get_auth_parameters_and_verify_success()
    {
        $this->data = collect($this->data)->only('cid')->toArray();

        $parameters = $this->authenticator->getAuthParams($this->data);

        $this->assertTrue($this->authenticator->verify($parameters));
    }

    private function generateSignature()
    {
        $this->data = array_change_key_case($this->data, CASE_LOWER);
        ksort($this->data, SORT_STRING);

        $this->signature = hash_hmac("sha256", http_build_query($this->data), $this->api_secret);;
    }
}