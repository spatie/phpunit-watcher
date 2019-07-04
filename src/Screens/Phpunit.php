<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Symfony\Component\Process\Process;
use Spatie\PhpUnitWatcher\Notification;

class Phpunit extends Screen
{
    const DEFAULT_BINARY_PATH = 'vendor/bin/phpunit';

    /** @var array */
    public $options;

    /** @var string */
    protected $phpunitArguments;

    /** @var string */
    private $phpunitBinaryPath;

    public function __construct(array $options)
    {
        $this->options = $options;

        $this->phpunitArguments = $options['phpunit']['arguments'] ?? '';

        $this->phpunitBinaryPath = $options['phpunit']['binaryPath'] ?? self::DEFAULT_BINARY_PATH;
    }

    public function draw(array $changedFilePaths = [])
    {
        $this->determineAutoFilter($changedFilePaths);

        if (! $this->options['autoFilter'] || ($this->options['autoFilter'] && ! empty($changedFilePaths))) {
            $this
                ->writeHeader()
                ->runTests();
        }

        if (! $this->options['autoFilter']) {
            $this->displayManual();
        }
    }

    public function registerListeners()
    {
        if ($this->options['autoFilter']) {
            return $this;
        }

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
        $result = (new Process("{$this->phpunitBinaryPath} {$this->phpunitArguments}"))
            ->setTty(true)
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

    public function determineAutoFilter(array $changedFilePaths = [])
    {
        $autoFilterOption = null;
        $this->phpunitArguments = isset($this->options['phpunit']['arguments']) ? $this->options['phpunit']['arguments'] : '';

        // Apply a filter based on the changed files
        if (! empty($changedFilePaths)) {
            $testNames = array_map(function ($filePath) {
                $filePathParts = explode('/', $filePath);
                $fileName = end($filePathParts);
                $fileNameParts = explode('.', $fileName);

                $testName = current($fileNameParts);

                // Suffix with "Test" if it's not already a test
                $strlen = strlen($testName);
                if ($strlen < 4 || ! (substr_compare(strtolower($testName), 'test', $strlen - 4, 4) === 0)) {
                    $testName .= 'Test';
                }

                return $testName;
            }, $changedFilePaths);

            $testFilterPattern = '/('.implode('|', $testNames).')/';
            $autoFilterOption = " --filter=\"$testFilterPattern\"";

            $this->phpunitArguments .= $autoFilterOption;
        }

        return $this;
    }

    public function getPhpunitArguments()
    {
        return isset($this->phpunitArguments) ? $this->phpunitArguments : null;
    }
}
