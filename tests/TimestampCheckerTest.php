<?php

use Lingxi\Signature\Checker\TimestampChecker;
use Lingxi\Signature\Checker\SignatureKeyChecker;

class TimestampCheckerTest extends PHPUnit_Framework_TestCase
{
    public function test_it_pass_when_time_pass_in_five_minutes_once()
    {
        $this->assertTrue(TimestampChecker::check(time() - 3600));
    }

    public function test_it_pass_when_time_pass_in_five_minutes_twice()
    {
        $this->assertTrue(TimestampChecker::check(time() - 3600), time() - 10);
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureTimestampException
     */
    public function test_it_throw_timestamp_exception_once()
    {
        TimestampChecker::check(time() - 3601);
    }

    /**
     * @expectedException \Lingxi\Signature\Exceptions\SignatureTimestampException
     */
    public function test_it_throw_timestamp_exception_twice()
    {
        TimestampChecker::check(time() - 3600, time() + 1);
    }
}
