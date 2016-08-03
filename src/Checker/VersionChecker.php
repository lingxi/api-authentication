<?php

namespace Lingxi\Signature\Checker;

use Lingxi\Signature\Interfaces\CheckerInterface;
use Lingxi\Signature\Exceptions\ApiVersionException;

class VersionChecker implements CheckerInterface
{
    public static function check($requestVersion, $version = null)
    {
        if ($requestVersion !== $version) {
            throw new ApiVersionException($requestVersion . '版本 api 不存在');
        }

        return true;
    }
}
