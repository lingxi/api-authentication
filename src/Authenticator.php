<?php

namespace Lingxi\Signature;

use Exception;
use Lingxi\Signature\Checker\VersionChecker;
use Lingxi\Signature\Checker\TimestampChecker;
use Lingxi\Signature\Checker\SignatureKeyChecker;
use Lingxi\Signature\Checker\SignatureValueChecker;
use Lingxi\Signature\Interfaces\AuthenticatorInterface;

class Authenticator implements AuthenticatorInterface
{
    protected $version;

    public function __construct($version = 'v1')
    {
        $this->version = $version;
    }

    public function attempt($params)
    {
        return $this->chechVersion($params['version'], $this->version)
             ->checkTimestamp($params['stamp'])
             ->checkSignatureKey($params['app_key'])
             ->checkSignatureValue($params);
    }

    protected function chechVersion($requestVersion, $version)
    {
        if (VersionChecker::check($requestVersion, $version)) {
            return $this;
        }
    }

    protected function checkTimestamp($timstamp)
    {
        if (TimestampChecker::check($timstamp)) {
            return $this;
        }
    }

    protected function checkSignatureKey($key)
    {
        if (! property_exists($this, 'api_secret') && ! method_exists($this, 'getSignatureApiSecret')) {
            throw new Exception('无法获取签名app_secret.');
        }

        if (SignatureKeyChecker::check($this->getSignatureApiSecret())) {
            return $this;
        }
    }

    protected function checkSignatureValue($params)
    {
        return SignatureValueChecker::check($params['signature'], $this->getSignatureValue($params));
    }

    public function getSignatureValue($params)
    {
        $paramsString = http_build_query($this->handleAllSignatureParamaters($params));

        return hash_hmac('sha256', $paramsString, $this->getSignatureApiSecret());
    }

    protected function handleAllSignatureParamaters($params)
    {
        $params = collect($params)->except(['signature', 'version'])->toArray();

        natsort($params);

        return $params;
    }

    /**
     * @test
     */
    protected function getSignatureApiSecret()
    {
        if (property_exists($this, 'api_secret')) {
            return $this->api_secret;
        }
    }

    public function __set($key, $value)
    {
        $this->{$key} = $value;

        return $this;
    }
}
