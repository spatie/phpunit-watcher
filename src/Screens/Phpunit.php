<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Spatie\PhpUnitWatcher\Notifier;
use Symfony\Component\Process\Process;

class Phpunit extends Screen
{
    /** @var string */
    protected $phpunitArguments;

    /** @var bool */
    protected $sendNotifications;

    public function __construct(string $phpunitArguments = '', bool $sendNotifications = true)
    {
        $this->phpunitArguments = $phpunitArguments;
        $this->sendNotifications = $sendNotifications;
    }

    public function draw()
    {
        $this
            ->writeHeader()
            ->runTests()
            ->displayManual();
    }

    public function registerListeners()
    {
        $this->terminal->onKeyPress(function ($line) {
            $line = strtolower($line);

            switch ($line) {
                case '':
                    $this->terminal->refreshScreen();
                    break;
                case 'a':
                    $this->terminal->displayScreen(new Phpunit());
                    break;
                case 't':
                    $this->terminal->displayScreen(new FilterTestName());
                    break;
                case 'p':
                    $this->terminal->displayScreen(new FilterFileName());
                    break;
                case 'q':
                    die();
                    break;
            }
        });

        return $this;
    }

    protected function writeHeader()
    {
        $title = 'Starting PHPUnit';

        if (! empty($this->phpunitArguments)) {
            $title .= " with arguments: `{$this->phpunitArguments}`";
        }

        $this->terminal
            ->comment($title)
            ->emptyLine();

        return $this;
    }

    protected function runTests()
    {
        $result = (new Process("vendor/bin/phpunit {$this->phpunitArguments}"))
            ->setTty(true)
            ->run(function ($type, $line) {
                echo $line;
            });

        if ($this->sendNotifications) {
            $result === 0 ? Notifier::testsPassed() : Notifier::testsFailed();
        }

        return $this;
    }

    protected function displayManual()
    {
        $this->terminal
            ->emptyLine()
            ->write('Press a to run all tests.')
            ->write('Press t to filter by test name.')
            ->write('Press p to filter by file name.')
            ->write('Press q to quit the watcher.')
            ->write('Press Enter to trigger a test run.');

        return $this;
    }
}
