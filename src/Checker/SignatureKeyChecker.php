<?php

namespace Lingxi\Signature\Checker;

use Lingxi\Signature\Interfaces\CheckerInterface;
use Lingxi\Signature\Exceptions\SignatureKeyException;

class SignatureKeyChecker implements CheckerInterface
{
    public static function check($secretFromDb, $secret = null)
    {
        if (! $secretFromDb) {
            throw new SignatureKeyException('api_key 不合法');
        }

        return true;
    }
}
