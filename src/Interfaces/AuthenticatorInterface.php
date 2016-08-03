<?php

namespace Lingxi\Signature\Interfaces;

interface AuthenticatorInterface
{
    public function attempt($params);

    public function getSignatureValue($params);
}
