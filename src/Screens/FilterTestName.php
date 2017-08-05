<?php

namespace Spatie\PhpUnitWatcher\Screens;

class FilterTestName extends Screen
{
    public function draw()
    {
        $this->terminal
            ->comment('Pattern mode usage')
            ->write('Type a pattern and press Enter to apply pattern to all tests.')
            ->write('Press Enter with an empty pattern to keep the current pattern.')
            ->emptyLine()
            ->comment('Start typing to filter by a test name.')
            ->prompt('pattern > ');

        return $this;
    }

    public function registerListeners()
    {
        $this->terminal->on('data', function ($line) {
            if ($line == '') {
                $this->terminal->goBack();

                return;
            }

            $phpunitArguments = "--filter={$line}";

            $phpunitScreen = $this->terminal->getPreviousScreen();

            $options = $phpunitScreen->options;

            $options['phpunit']['arguments'] = $phpunitArguments;

            $this->terminal->displayScreen(new Phpunit($options));
        });

        return $this;
    }
}
