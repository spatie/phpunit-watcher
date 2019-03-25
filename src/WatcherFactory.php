<?php

namespace Spatie\PhpUnitWatcher;

use InvalidArgumentException;
use Symfony\Component\Finder\Finder;

class WatcherFactory
{
    public static function create(array $options = []): array
    {
        $options = static::mergeWithDefaultOptions($options);

        if (empty($options['watch']['directories'])) {
            throw new InvalidArgumentException(
                'The watch directories do not exist. Make sure you are running the watcher from '.
                'the root of your project, or create a custom config file.'
            );
        }

        $finder = (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->name($options['watch']['fileMask'])
            ->files()
            ->in($options['watch']['directories']);

        $watcher = new Watcher($finder, $options);

        return [$watcher, $options];
    }

    protected static function mergeWithDefaultOptions(array $options): array
    {
        $options = array_merge([

            'watch' => [
                'directories' => [
                    'app',
                    'src',
                    'tests',
                ],
                'fileMask' => '*.php',
            ],
            'notifications' => [
                'passingTests' => true,
                'failingTests' => true,
            ],
            'hideManual' => false,
        ], $options);

        $options['watch']['directories'] = array_map(function ($directory) {
            return getcwd()."/{$directory}";
        }, $options['watch']['directories']);

        $options['watch']['directories'] = array_filter($options['watch']['directories'], function ($directory) {
            return file_exists($directory);
        });

        return $options;
    }
}
