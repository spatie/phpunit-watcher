<?php

namespace Spatie\PhpUnitWatcher;

use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;
use Spatie\PhpUnitWatcher\Screens\Phpunit;
use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\Crc32ContentHash;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;
use Yosymfony\ResourceWatcher\ResourceWatcher;

class Watcher
{
    /** @var \Symfony\Component\Finder\Finder */
    protected $finder;

    /** @var \React\EventLoop\LibEventLoop */
    protected $loop;

    /** @var \Spatie\PhpUnitWatcher\Terminal */
    protected $terminal;

    /** @var array */
    protected $options;

    public function __construct(Finder $finder, array $options)
    {
        $this->finder = $finder;

        $this->loop = Factory::create();

        $this->terminal = new Terminal($this->buildStdio());

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

            if ($watcher->findChanges()->hasChanges()) {
                $this->terminal->refreshScreen();
            }
        });

        $this->loop->run();
    }

    protected function buildStdio()
    {
        $output = null;

        if (OS::isOnWindows()) {
            // Interaction on windows is currently not supported
            fclose(STDIN);

            // Simple fix for windows compatibility since we don't write a lot of data at once
            // https://github.com/clue/reactphp-stdio/issues/83#issuecomment-546678609
            $output = new ThroughStream(static function ($data) {
                echo $data;
            });
        }

        return new Stdio($this->loop, null, $output);
    }
}
