<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Symfony\Component\Process\Process;
use Spatie\PhpUnitWatcher\Notification;

class Phpunit extends Screen
{
    /** @var array */
    public $options;

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
                    $this->options['phpunit']['arguments'] = '';

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
                default:
                    $this->registerListeners();
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
            ->write('<dim>Press </dim>a<dim> to run all tests.</dim>')
            ->write('<dim>Press </dim>t<dim> to filter by test name.</dim>')
            ->write('<dim>Press </dim>p<dim> to filter by file name.</dim>')
            ->write('<dim>Press </dim>q<dim> to quit the watcher.</dim>')
            ->write('<dim>Press </dim>Enter<dim> to trigger a test run.</dim>');

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
