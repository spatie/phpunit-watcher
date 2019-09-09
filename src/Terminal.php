<?php

namespace Spatie\PhpUnitWatcher;

use Clue\React\Stdio\Stdio;
use Spatie\PhpUnitWatcher\Screens\Screen;
use Symfony\Component\Console\Formatter\OutputFormatter;

class Terminal
{
    /** @var \Clue\React\Stdio\Stdio */
    protected $io;

    /** @var \Spatie\PhpUnitWatcher\Screens\Screen */
    protected $previousScreen = null;

    /** @var \Spatie\PhpUnitWatcher\Screens\Screen */
    protected $currentScreen = null;

    public function __construct(Stdio $io)
    {
        $this->io = $io;
    }

    public function on(string $eventName, callable $callable)
    {
        $this->io->on($eventName, function ($line) use ($callable) {
            $callable(trim($line));
        });

        return $this;
    }

    public function onKeyPress(callable $callable)
    {
        $this->io->once('data', function ($line) use ($callable) {
            $callable(trim($line));
        });

        return $this;
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

        $formattedMessage = str_replace('<dim>', "\e[2m", $formattedMessage);
        $formattedMessage = str_replace('</dim>', "\e[22m", $formattedMessage);

        $this->io->write($formattedMessage.PHP_EOL);

        return $this;
    }

    public function displayScreen(Screen $screen, $clearScreen = true)
    {
        $this->previousScreen = $this->currentScreen;

        $this->currentScreen = $screen;

        $screen
            ->useTerminal($this)
            ->clearPrompt()
            ->removeAllListeners()
            ->registerListeners();

        if ($clearScreen) {
            $screen->clear();
        }

        $screen->draw();

        return $this;
    }

    public function goBack()
    {
        if (is_null($this->previousScreen)) {
            return;
        }

        $this->currentScreen = $this->previousScreen;

        $this->displayScreen($this->currentScreen);

        return $this;
    }

    public function getPreviousScreen(): Screen
    {
        return $this->previousScreen;
    }

    public function refreshScreen()
    {
        if (is_null($this->currentScreen)) {
            return;
        }

        $this->displayScreen($this->currentScreen);

        return $this;
    }

    public function isDisplayingScreen(string $screenClassName): bool
    {
        if (is_null($this->currentScreen)) {
            return false;
        }

        return $screenClassName === get_class($this->currentScreen);
    }

    public function removeAllListeners()
    {
        $this->io->removeAllListeners();

        return $this;
    }

    public function prompt(string $prompt)
    {
        $this->getReadline()->setPrompt($prompt);

        return $this;
    }

    public function clearPrompt()
    {
        $this->getReadline()->setPrompt('');

        return $this;
    }

    public function getReadline()
    {
        return $this->io->getReadline();
    }

    public function getStdio()
    {
        return $this->io;
    }
}
