<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Clue\React\Stdio\Readline;

class FilterScreen extends Screen
{
    public function draw()
    {
        $this->terminal
            ->comment('Pattern mode usage')
            ->write('Type a pattern and press Enter to apply pattern to all tests.')
            ->write('Press Enter with an empty pattern to keep the current pattern.')
            ->emptyLine()
            ->comment('Start typing to filter by a test name regex pattern.')
            ->prompt('pattern > ');
    }

    public function registerListeners()
    {
        $this->terminal->on('data', function ($line) {
            if ($line == "") {
                $this->terminal->goBack();

                return;
            }

            $phpunitArguments = "--filter={$line}";

            $this->terminal->displayScreen(new PhpunitScreen($phpunitArguments));
        });
    }


}