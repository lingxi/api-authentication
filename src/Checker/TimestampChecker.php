<?php

namespace Lingxi\Signature\Checker;

use Lingxi\Signature\Interfaces\CheckerInterface;
use Lingxi\Signature\Exceptions\SignatureTimestampException;

class TimestampChecker implements CheckerInterface
{
    const TIME_EXPIRED = 3000;

    public static function check($timestamp, $now = null)
    {
        if (! $now) {
            $now = time();
        }

        if ($now - $timestamp > self::TIME_EXPIRED) {
            throw new SignatureTimestampException('请求时间戳过期');
        }

        return true;
    }
}
