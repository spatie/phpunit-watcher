<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Symfony\Component\Process\Process;

class PhpunitScreen extends Screen
{
    /** @var string */
    protected $phpunitArguments;

    public function __construct(string $phpunitArguments = "")
    {
        $this->phpunitArguments = $phpunitArguments;
    }

    public function draw()
    {
        $this->writeHeader();
        $this->runTests();
        $this->displayManual();
    }

    public function registerListeners()
    {
        $this->terminal->on('data', function ($line) {

            $line = trim(strtolower($line));

            switch ($line) {
                case "":
                    $this->terminal->displayScreen($this);
                    break;
                case "t":
                    $this->terminal->displayScreen(new FilterScreen());
                    break;
                case "q":
                    die();
                    break;
            }
        });

        return $this;
    }

    protected function writeHeader()
    {
        $title = 'Starting PHPUnit';

        if (!empty($this->phpunitArguments)) {
            $title .= " with arguments: `{$this->phpunitArguments}`";
        }

        $this->terminal
            ->comment($title)
            ->emptyLine();
    }

    protected function runTests()
    {
        (new Process("vendor/bin/phpunit {$this->phpunitArguments}"))
            ->setTty(true)
            ->run(function ($type, $line) {
                echo $line;
            });
    }

    protected function displayManual()
    {
        $this->terminal
            ->emptyLine()
            ->write('Press a to run all tests.')
            ->write('Press t to filter by a test name regex pattern')
            ->write('Press q to quit the watcher.')
            ->write('Press Enter to trigger a test run');
    }
}