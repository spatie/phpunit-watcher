<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class WatcherFactory
{
    public static function create(array $options = []): array
    {
        $options = static::mergeWithDefaultOptions($options);


        $finder = (new Finder())
            ->name($options['watch']['fileMask'])
            ->files()
            ->in($options['watch']['directories']);

        $watcher = (new Watcher($finder));

        if (isset($options['cache'])) {
            $watcher->useCacheFile($options['cache']);
        }

        if (isset($options['phpunitArguments'])) {
            $watcher->usePhpunitArguments($options['phpunitArguments']);
        }

        return [$watcher, $options];
    }

    protected static function mergeWithDefaultOptions(array $options): array
    {
        $options = array_merge([
            'watch' => [
                'directories' => [
                    'src',
                    'tests',
                ],
                'fileMask' => '*.php',
            ],
            'cache' => '.phpunit-watcher-cache.php',
        ], $options);

        foreach ($options['watch']['directories'] as $index => $directory) {
            $options['watch']['directories'][$index] = getcwd()."/{$directory}";
        }

        return $options;
    }
}
