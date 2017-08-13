<?php

namespace Spatie\PhpUnitWatcher;

trait ArgumentAccessors
{
    public function setTestFile($testFile)
    {
        $this->testFile = $testFile;
    }

    public function setFilter($query)
    {
        $this->addArgument('--filter', $query, ' ');
    }
}
