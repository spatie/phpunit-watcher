<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Symfony\Component\Process\Process;
use Spatie\PhpUnitWatcher\Notification;

class Phpunit extends Screen
{
    /** @var array */
    protected $options;

    /** @var string */
    protected $phpunitArguments;

    public function __construct(array $options)
    {
        $this->options = $options;

        $this->phpunitArguments = $options['phpunit']['arguments'] ?? '';
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
                    $this->terminal->displayScreen(new Phpunit($this->options));
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

        $this->sendDesktopNotification($result);

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

    protected function sendDesktopNotification(int $result)
    {
        $notificationName = $result === 0
            ? 'passingTests'
            : 'failingTests';

        if ($this->options['notifications'][$notificationName]) {
            Notification::create()->$notificationName();
        }
    }
}
