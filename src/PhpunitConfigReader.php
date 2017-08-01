<?php

namespace Spatie\PhpUnitWatcher;

use PHPUnit\Util\Configuration;

class PhpunitConfigReader
{
    public static function getAllTestNames(): array
    {
        $configuration = Configuration::getInstance(__DIR__ . '/../phpunit.xml.dist');

        $tests = $configuration->getTestSuiteConfiguration()->tests();

        dd($tests);
    }
}