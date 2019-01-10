<?php

namespace Spatie\PhpUnitWatcher;

use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use Symfony\Component\Finder\Finder;
use Spatie\PhpUnitWatcher\Screens\Phpunit;
use Yosymfony\ResourceWatcher\Crc32ContentHash;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;

class Watcher
{
    /** @var \Symfony\Component\Finder\Finder */
    protected $finder;

    /** @var \React\EventLoop\ExtLibeventLoop */
    protected $loop;

    /** @var \Spatie\PhpUnitWatcher\Terminal */
    protected $terminal;

    /** @var array */
    protected $options;

    public function __construct(Finder $finder, array $options)
    {
        $this->finder = $finder;

        $this->loop = Factory::create();

        $this->terminal = new Terminal(new Stdio($this->loop));

        $this->options = $options;
    }

    public function startWatching()
    {
        $this->terminal->displayScreen(new Phpunit($this->options), false);

        $watcher = new ResourceWatcher(new ResourceCacheMemory(), $this->finder, new Crc32ContentHash());

        $this->loop->addPeriodicTimer(1 / 4, function () use ($watcher) {
            if (! $this->terminal->isDisplayingScreen(Phpunit::class)) {
                return;
            }

            $watcher->findChanges();

            if ($watcher->findChanges()->hasChanges()) {
                $this->terminal->refreshScreen();
            }
        });

        $this->loop->run();
    }
}
