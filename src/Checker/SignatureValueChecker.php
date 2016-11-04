<?php

namespace Lingxi\Signature\Checker;

use Lingxi\Signature\Interfaces\CheckerInterface;
use Lingxi\Signature\Exceptions\SignatureValueException;

class SignatureValueChecker implements CheckerInterface
{
    public static function check($requestSignatureValue, $signatureValue = null)
    {
        if ($requestSignatureValue !== $signatureValue) {
            throw new SignatureValueException('签名错误');
        }

        return true;
    }
}
