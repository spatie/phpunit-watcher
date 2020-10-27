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
            ->ignoreDotFiles($options['watch']['ignoreDotFiles'])
            ->ignoreVCS($options['watch']['ignoreVCS'])
            ->ignoreVCSIgnored($options['watch']['ignoreVCSIgnored'])
            ->name($options['watch']['fileMask'])
            ->files()
            ->exclude($options['watch']['exclude'])
            ->in($options['watch']['directories']);

        $watcher = new Watcher($finder, $options);

        return [$watcher, $options];
    }

    public static function getDefaultOptions(): array
    {
        return [
            'watch' => [
                'directories' => [
                    'app',
                    'src',
                    'tests',
                ],
                'exclude' => [],
                'ignoreDotFiles' => false,
                'ignoreVCS' => false,
                'ignoreVCSIgnored' => false,
                'fileMask' => '*.php',
            ],
            'notifications' => [
                'passingTests' => true,
                'failingTests' => true,
            ],
            'hideManual' => false,
        ];
    }

    protected static function mergeWithDefaultOptions(array $userOptions): array
    {
        // Merge all options with the defaults, so that there's always a complete set.
        $mergedOptions = array_replace_recursive(self::getDefaultOptions(), $userOptions);

        // Exception to above: Allow directories to be overwritten entirely, because that's usually desired.
        if (isset($userOptions['watch']['directories'])) {
            $mergedOptions['watch']['directories'] = $userOptions['watch']['directories'];
        }

        $mergedOptions['watch']['directories'] = array_unique($mergedOptions['watch']['directories']);

        $mergedOptions['watch']['directories'] = array_map(function ($directory) {
            return getcwd()."/{$directory}";
        }, $mergedOptions['watch']['directories']);

        $mergedOptions['watch']['directories'] = array_filter($mergedOptions['watch']['directories'], function ($directory) {
            return file_exists($directory);
        });

        return $mergedOptions;
    }
}
