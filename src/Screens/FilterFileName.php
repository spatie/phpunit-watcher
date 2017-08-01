<?php

namespace Spatie\PhpUnitWatcher\Screens;

class FilterFileName extends Screen
{
    public function draw()
    {
        $this->terminal
            ->comment('Pattern mode usage')
            ->write('Type a pattern and press Enter to only run tests in the giving path or file.')
            ->write('Press Enter with an empty pattern to execute all tests in all files.')
            ->emptyLine()
            ->comment('Start typing to filter by a test name.')
            ->prompt('pattern > ');
    }

    public function registerListeners()
    {
        $this->terminal->on('data', function ($line) {
            $line = trim($line);

            if ($line == '') {
                $this->terminal->goBack();

                return;
            }

            $phpunitArguments = "{$line}";

            $this->terminal->displayScreen(new Phpunit($phpunitArguments));
        });
    }
}
