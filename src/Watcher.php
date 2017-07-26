<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\ResourceCacheFile;
use Yosymfony\ResourceWatcher\ResourceWatcher;

class Watcher
{
    /** @var \Symfony\Component\Finder\Finder  */
    protected $finder;

    /** @var string */
    protected $pathToCacheFile;

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
        passthru('vendor/bin/phpunit');
    }

    protected function clearScreen()
    {
        passthru("echo '\033\143'");
    }
}