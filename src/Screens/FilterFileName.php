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

        return $this;
    }

    public function registerListeners()
    {
        $readline = $this->terminal->getReadline();

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

        $this->registerAutocompleter($readline);

        return $this;
    }

    protected function registerAutocompleter($readline)
    {
        $readline->setAutocomplete(function ($word, $startOffset, $endOffset) use ($readline) {
            $input = $readline->getInput();

            $paths = glob("$word*", GLOB_MARK);

            if (empty($paths)) {
                return;
            }

            if (count($paths) > 1) {
                $this->terminal->getStdio()->write(implode('  ', $paths)."\n");

                return;
            }

            $path = $paths[0];

            $lineStart = mb_substr($input, 0, $startOffset);
            $lineEnd = mb_substr($input, $endOffset);

            $path = $this->sanitzeOffset($startOffset, $path, $input);

            $newInput = $lineStart.$path.$lineEnd;

            $readline->setInput($newInput);

            $readline->moveCursorTo($startOffset + mb_strlen($path));
        });
    }

    protected function sanitzeOffset($startOffset, $path, $input): string
    {
        if ($startOffset > 0 && mb_strlen($path) > 1 && mb_substr($path, -1) != DIRECTORY_SEPARATOR) {
            $previousChar = mb_substr($input, $startOffset - 1, 1);
            if ($previousChar === '"' || $previousChar === '\'') {
                $path .= $previousChar;
            }
        }

        return $path;
    }
}
