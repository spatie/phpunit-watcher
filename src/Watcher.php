<?php

namespace Spatie\PhpUnitWatcher;

use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use Symfony\Component\Finder\Finder;
use Spatie\PhpUnitWatcher\Screens\Phpunit;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;

class Watcher
{
    /** @var \Symfony\Component\Finder\Finder */
    protected $finder;

    /** @var $string */
    protected $phpunitArguments;

    /** @var \React\EventLoop\LibEventLoop */
    protected $loop;

    /** @var \Spatie\PhpUnitWatcher\Terminal */
    protected $terminal;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        $this->loop = Factory::create();

        $this->terminal = new Terminal(new Stdio($this->loop));
    }

    public function usePhpunitArguments(string $arguments)
    {
        $this->phpunitArguments = $arguments;

        return $this;
    }

    public function startWatching()
    {
        $this->terminal->displayScreen(new Phpunit($this->phpunitArguments), false);

        $watcher = new ResourceWatcher(new ResourceCacheMemory());

        $watcher->setFinder($this->finder);

        $this->loop->addPeriodicTimer(1 / 4, function () use ($watcher) {
            if (! $this->terminal->isDisplayingScreen(Phpunit::class)) {
                return;
            }

            $watcher->findChanges();

            if ($watcher->hasChanges()) {
                $this->terminal->refreshScreen();
            }
        });

        $this->loop->run();
    }
}
