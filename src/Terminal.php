<?php

namespace Spatie\PhpUnitWatcher;

use Clue\React\Stdio\Stdio;
use Spatie\PhpUnitWatcher\Screens\Screen;
use Symfony\Component\Console\Formatter\OutputFormatter;

class Terminal
{
    /**  @var \Clue\React\Stdio\Stdio */
    protected $io;

    protected $displayingScreen = null;

    public function __construct(Stdio $io)
    {
        $this->io = $io;
    }

    public function onEnter(callable $callable)
    {
        $this->io->on('data', function ($line) use ($callable) {
            echo 'on callable';
            $callable($line);
        });
    }

    public function emptyLine()
    {
        $this->write('');

        return $this;
    }

    public function comment(string $message)
    {
        $this->write($message, 'comment');

        return $this;
    }

    public function write(string $message = '', $level = null)
    {
        if ($level != '') {
            $message = "<{$level}>$message</{$level}>";
        }

        $formattedMessage = (new OutputFormatter(true))->format($message);

        $this->io->writeln($formattedMessage);

        return $this;
    }

    public function displayScreen(Screen $screen)
    {
        $this->displayingScreen = $screen;

        $screen
            ->useTerminal($this)
            ->removeAllListeners()
            ->registerListeners()
            ->clear()
            ->draw();
    }

    public function isDisplayingScreen(string $screenClassName): bool
    {
        if (is_null($this->displayingScreen)) {
            return false;
        }

        return $screenClassName === get_class($this->displayingScreen);
    }

    public function removeAllListeners()
    {
        $this->io->removeAllListeners();

        return $this;
    }
}
