<?php

namespace Spatie\PhpUnitWatcher\Screens;

class Phpunit extends Screen
{
    /** @var callable */
    protected $runTests;

    public function __construct(callable $runTests)
    {
        $this->runTests = $runTests;
    }

    public function draw()
    {
        $this->writeHeader();
        ($this->runTests)();
        $this->displayManual();
    }

    public function registerListeners()
    {
        $this->terminal->onEnter(function ($line) {
            $this->terminal->displayScreen($this);
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

    protected function displayManual()
    {
        $this->terminal
            ->emptyLine()
            ->write('Press enter to run tests again');
    }
}