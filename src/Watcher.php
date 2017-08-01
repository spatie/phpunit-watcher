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

    /** @var \Clue\React\Stdio\Stdio */
    protected $io;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        $this->loop = Factory::create();

        $this->io = new Stdio($this->loop);
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

        $this->io->on('data', function ($line) {
            $this->runTestsAndRebuildScreen();
        });

        $this->loop->run();
    }

    protected function runTestsAndRebuildScreen(bool $clearScreenFirst = true)
    {
        if ($clearScreenFirst) {
            $this->clearScreen();
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

        $this->writeToScreen($title, 'comment');
        $this->writeToScreen('');
    }

    protected function clearScreen()
    {
        passthru("echo '\033\143'");
    }

    protected function displayManual()
    {
        $this->writeToScreen();
        $this->writeToScreen('Press enter to run tests again', 'comment');
    }

    protected function writeToScreen($message = '', $level = null)
    {
        if ($level != '') {
            $message = "<{$level}>$message</{$level}>";
        }

        $formattedMessage = (new OutputFormatter(true))->format($message);

        $this->io->writeln($formattedMessage);
    }
}
