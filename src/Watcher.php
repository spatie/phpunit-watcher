<?php

namespace Spatie\PhpUnitWatcher;

use Clue\React\Stdio\Stdio;
use React\EventLoop\Factory;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
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
        $this->runTestsAndRebuildScreen(false);

        $watcher = new ResourceWatcher(new ResourceCacheMemory());

        $watcher->setFinder($this->finder);

        $this->loop->addPeriodicTimer(1 / 4, function () use ($watcher) {
            $watcher->findChanges();

            if ($watcher->hasChanges()) {
                $this->runTestsAndRebuildScreen();
            }
        });

        $this->terminal->onEnter(function ($line) {
            $this->runTestsAndRebuildScreen();
        });

        $this->loop->run();
    }

    protected function runTestsAndRebuildScreen(bool $clearScreenFirst = true)
    {
        if ($clearScreenFirst) {
            $this->terminal->clear();
        }

        $this->writeTestRunHeader();
        $this->runTests();
        $this->displayManual();
    }

    protected function runTests()
    {
        (new Process("vendor/bin/phpunit {$this->phpunitArguments}"))
            ->setTty(true)
            ->run(function ($type, $line) {
                echo $line;
            });
    }

    private function writeTestRunHeader()
    {
        $title = 'Starting PHPUnit';

        if (! empty($this->phpunitArguments)) {
            $title .= " with arguments: `{$this->phpunitArguments}`";
        }

        $this->terminal
            ->comment($title)
            ->emptyLine();
    }

    protected function displayManual()
    {
        $this->terminal
            ->emptyLine()
            ->write('Press enter to run tests again');
    }
}
