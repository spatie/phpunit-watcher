<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;

class Watcher
{
    /** @var \Symfony\Component\Finder\Finder */
    protected $finder;

    /** @var $string */
    protected $phpunitArguments;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function usePhpunitArguments(string $arguments)
    {
        $this->phpunitArguments = $arguments;

        return $this;
    }

    public function startWatching()
    {
        $this->runTests();

        $watcher = new ResourceWatcher(new ResourceCacheMemory());

        $watcher->setFinder($this->finder);

        while (true) {
            $watcher->findChanges();

            if ($watcher->hasChanges()) {
                $this->clearScreen();
                $this->runTests();
            }

            usleep(250000);
        }
    }

    protected function clearScreen()
    {
        passthru("echo '\033\143'");
    }

    protected function runTests()
    {
        (new Process("vendor/bin/phpunit {$this->phpunitArguments}"))
            ->setTty(true)
            ->run(function ($type, $line) {
                echo $line;
            });
    }
}
