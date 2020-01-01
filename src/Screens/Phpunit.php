<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Spatie\PhpUnitWatcher\Notification;
use Symfony\Component\Process\Process;

class Phpunit extends Screen
{
    const DEFAULT_BINARY_PATH = 'vendor/bin/phpunit';

    /** @var array */
    public $options;

    /** @var string */
    protected $phpunitArguments;

    /** @var string */
    private $phpunitBinaryPath;

    /** @var int */
    private $phpunitTimeout;

    public function __construct(array $options)
    {
        $this->options = $options;

        $this->phpunitArguments = $options['phpunit']['arguments'] ?? '';
        $this->phpunitBinaryPath = $options['phpunit']['binaryPath'] ?? self::DEFAULT_BINARY_PATH;
        $this->phpunitTimeout = $options['phpunit']['timeout'] ?? 60;
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

                    $this->terminal->displayScreen(new self($this->options));
                    break;
                case 'g':
                    $this->terminal->displayScreen(new FilterGroupName());
                    break;
                case 's':
                    $this->terminal->displayScreen(new FilterTestSuiteName());
                    break;
                case 't':
                    $this->terminal->displayScreen(new FilterTestName());
                    break;
                case 'p':
                    $this->terminal->displayScreen(new FilterFileName());
                    break;
                case 'r':
                    $this->terminal->displayScreen(new RandomSeed());
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
        $result = (new Process(array_merge([$this->phpunitBinaryPath], explode(' ', $this->phpunitArguments))))
            ->setTimeout($this->phpunitTimeout)
            ->setTty(Process::isTtySupported())
            ->run(function ($type, $line) {
                echo $line;
            });

        $this->sendDesktopNotification($result);

        return $this;
    }

    protected function displayManual()
    {
        if ($this->options['hideManual']) {
            return $this;
        }

        $this->terminal
            ->emptyLine()
            ->write('<dim>Press </dim>a<dim> to run all tests.</dim>')
            ->write('<dim>Press </dim>t<dim> to filter by test name.</dim>')
            ->write('<dim>Press </dim>p<dim> to filter by file name.</dim>')
            ->write('<dim>Press </dim>g<dim> to filter by group name.</dim>')
            ->write('<dim>Press </dim>s<dim> to filter by test suite name.</dim>')
            ->write('<dim>Press </dim>r<dim> to run tests with a random seed.</dim>')
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
