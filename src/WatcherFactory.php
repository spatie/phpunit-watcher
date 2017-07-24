<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Finder\Finder;

class WatcherFactory
{
    public function create(array $config): Watcher
    {
        $finder = new Finder();

        $finder->files()
            ->in([
                __DIR__ . "/src",
                __DIR__ . "/tests",
            ]);

        $watcher = (new Watcher($finder));

        if (isset($config['pathToWatchFile'])) {
            $watcher->useCacheFile(config($config['pathToWatchFile']));
        }

        return $watcher;
    }
}