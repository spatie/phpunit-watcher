<?php

use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\ResourceCacheFile;
use Yosymfony\ResourceWatcher\ResourceWatcher;

include 'vendor/autoload.php';

$finder = new Finder();

$finder->files()
    ->in([
        __DIR__ . "/src",
    ]);

$cache = new ResourceCacheFile(
    __DIR__ . "/.test-changes.php"
);

$watcher = new ResourceWatcher($cache);

$watcher->setFinder($finder);

while (true) {
    $watcher->findChanges();

    if ($watcher->hasChanges()) {
        echo 'yow';
    }
}