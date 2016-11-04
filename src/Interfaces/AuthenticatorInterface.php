<?php

namespace Lingxi\Signature\Interfaces;

interface AuthenticatorInterface
{
    public function attempt($params);

    public function getSignatureValue($params);

    public function getAuthParams($params);

    public function verify($params);
}
