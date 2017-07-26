<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Yosymfony\ResourceWatcher\ResourceCacheFile;
use Yosymfony\ResourceWatcher\ResourceWatcher;

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

        return;
    }

    public function usePhpunitArguments(string $arguments)
    {
        $this->phpunitArguments = $arguments;

        return $this;
    }

    public function startWatching()
    {
        $this->clearScreen();
        $this->rerunTests();

        $cache = new ResourceCacheFile(
            $this->pathToCacheFile
        );

        $watcher = new ResourceWatcher($cache);

        $watcher->setFinder($this->finder);

        while (true) {
            $watcher->findChanges();

            if ($watcher->hasChanges()) {
                $this->clearScreen();
                $this->rerunTests();
            }
        }
    }

    protected function rerunTests()
    {
        (new Process("vendor/bin/phpunit {$this->phpunitArguments}"))
            ->setTty(true)
            ->run(function ($type, $line) {
                echo $line;
            });
    }

    protected function clearScreen()
    {
        passthru("echo '\033\143'");
    }
}