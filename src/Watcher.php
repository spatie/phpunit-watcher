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
        $cache = new ResourceCacheFile(
            $this->pathToCacheFile
        );

        $watcher = new ResourceWatcher($cache);

        $watcher->setFinder($this->finder);

        while (true) {
            $watcher->findChanges();

            if ($watcher->hasChanges()) {
                $this->rerunTests();
            }
        }
    }

    protected function rerunTests()
    {
        echo 'rerun phpunit tests';
    }
}