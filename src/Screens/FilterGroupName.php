<?php

namespace Spatie\PhpUnitWatcher\Screens;

class FilterGroupName extends Screen
{
    public function draw()
    {
        $this->terminal
            ->comment('Pattern mode usage')
            ->write('Type a pattern and press Enter to only run tests from a specific PHPUnit group.')
            ->write('Press Enter with an empty pattern to execute all tests in all files.')
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

            $phpunitArguments = "--group={$line}";

            // if need to do anything here, then make sure you also do it in the other filters. maybe find a way to make it DRY

            $phpunitScreen = $this->terminal->getPreviousScreen();

            $options = $phpunitScreen->options;

            var_dump( $options['phpunit']['arguments'], $phpunitArguments );
            $options['phpunit']['arguments'] .= $phpunitArguments;

			var_dump( $options['phpunit']['arguments'] );
			die();

            $this->terminal->displayScreen(new Phpunit($options));
        });

        return $this;
    }
}
