<?php

namespace Spatie\PhpUnitWatcher\Screens;

use Spatie\PhpUnitWatcher\Screens\Completers\FilenameCompleter;

class FilterFileName extends Screen
{
    public function draw()
    {
        $this->terminal
            ->comment('Pattern mode usage')
            ->write('Type a pattern and press Enter to only run tests in the giving path or file.')
            ->write('Press Enter with an empty pattern to execute all tests in all files.')
            ->emptyLine()
            ->comment('Start typing to filter by file name.')
            ->prompt('pattern > ');

        return $this;
    }

    public function registerListeners()
    {
        $this->registerDataListener();

        $this->registerAutocompleter();

        return $this;
    }

    protected function registerDataListener()
    {
        $this->terminal->on('data', function ($line) {
            if ($line == '') {
                $this->terminal->goBack();

                return;
            }

            $phpunitArguments = "{$line}";

            $phpunitScreen = $this->terminal->getPreviousScreen();

            $options = $phpunitScreen->options;

            $options['phpunit']['arguments'] = $phpunitArguments;

            $this->terminal->displayScreen(new Phpunit($options));
        });
    }

    protected function registerAutocompleter()
    {
        $readline = $this->terminal->getReadline();

        $filenameAutocompleter = new FilenameCompleter($readline);

        $filenameAutocompleter->onSuggestions(function ($suggestions) {
            $this->refreshScreenWithSuggestions($suggestions, 10);
        });

        $readline->setAutocomplete($filenameAutocompleter);
    }

    protected function refreshScreenWithSuggestions($suggestions, $limit)
    {
        $firstSuggestions = array_slice($suggestions, 0, $limit);

        $this->terminal->refreshScreen();

        $stdio = $this->terminal->getStdio();

        $stdio->write("\n".implode("\n", $firstSuggestions)."\n");

        $count = count($suggestions) - $limit;
        if ($count > 0) {
            $stdio->write("(+$count others)\n");
        }

        $stdio->write("\n");
    }
}
