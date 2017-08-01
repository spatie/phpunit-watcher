<?php

namespace Spatie\PhpUnitWatcher;

use Clue\React\Stdio\Stdio;
use Symfony\Component\Console\Formatter\OutputFormatter;

class Terminal
{
    /**  @var \Clue\React\Stdio\Stdio */
    protected $io;

    public function __construct(Stdio $io)
    {
        $this->io = $io;
    }

    public function onEnter(callable $callable)
    {
        $this->io->on('data', function ($line) use ($callable) {
            $callable($line);
        });
    }

    public function clear()
    {
        passthru("echo '\033\143'");

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

        $this->io->writeln($formattedMessage);

        return $this;
    }
}