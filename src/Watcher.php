<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheFile;

class Watcher
{
    /** @var \Symfony\Component\Finder\Finder */
    protected $finder;

    /** @var string */
    protected $pathToCacheFile;

    /** @var $string */
    protected $phpunitArguments;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        $this->pathToCacheFile = '.phpunit-watcher-cache.php';
    }

    public function useCacheFile(string $pathToCacheFile)
    {
        $this->pathToCacheFile = $pathToCacheFile;
    }

    public function usePhpunitArguments(string $arguments)
    {
        $this->phpunitArguments = $arguments;

        return $this;
    }

    public function startWatching()
    {
        $this->runTests();

        $cache = new ResourceCacheFile(
            $this->pathToCacheFile
        );

        $watcher = new ResourceWatcher($cache);

        $watcher->setFinder($this->finder);

        while (true) {
            $watcher->findChanges();

            if ($watcher->hasChanges()) {
                $this->clearScreen();
                $this->runTests();
            }
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
