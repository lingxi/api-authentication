<?php

namespace Lingxi\Signature\Interfaces;

interface CheckerInterface
{
    public static function check($requestValue, $value = null);
}
