<?php

namespace Spatie\PhpUnitWatcher\Screens;

class RandomSeed extends Screen
{
    public function draw()
    {
        $this->terminal
            ->comment('Random seed usage')
            ->write('Type a seed and press Enter to run tests in random order but with a specific seed.')
            ->write('Press Enter with an empty pattern to execute all tests in random order.')
            ->emptyLine()
            ->comment('Start typing to add a random seed')
            ->prompt('seed > ');

        return $this;
    }

    public function registerListeners()
    {
        $this->terminal->on('data', function ($line) {
            $phpunitArguments = '--order-by=random';
            // merge w/ config. here, or lower down just before set options?
	        // leaning towards down below
	        // nah here seems fine

            if ($line !== '') {
                $phpunitArguments .= " --random-order-seed={$line}";
            }

            $phpunitScreen = $this->terminal->getPreviousScreen();

            $options = $phpunitScreen->options;

            $options['phpunit']['arguments'] = $phpunitArguments;

            $this->terminal->displayScreen(new Phpunit($options));
        });

        return $this;
    }
}
