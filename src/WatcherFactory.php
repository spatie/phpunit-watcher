<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Finder\Finder;

class WatcherFactory
{
    public static function create(array $config): Watcher
    {
        $finder = new Finder();

        $finder
            ->name('*.php')
            ->files()
            ->in([
                getcwd() . "/src",
                getcwd() . "/tests",
            ]);

        $watcher = (new Watcher($finder));

        if (isset($config['pathToWatchFile'])) {
            $watcher->useCacheFile(config($config['pathToWatchFile']));
        }

        return $watcher;
    }
}