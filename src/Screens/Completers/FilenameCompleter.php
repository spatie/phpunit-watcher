<?php

namespace Spatie\PhpUnitWatcher\Screens\Completers;

class FilenameCompleter extends Completer
{
    /**
     * Search for files and directories corresponding to the word to complete.
     *
     * @return array
     */
    protected function search()
    {
        return glob($this->word.'*', GLOB_MARK) ?: [];
    }

    /**
     * Append a quote if path is prefixed with a quote, except for directories.
     *
     * @param string $path
     * @return string
     */
    protected function appendQuoteIfNeeded($path)
    {
        if (is_dir($path)) {
            return $path;
        }

        $char = $this->getCharBeforeWord();

        return ($char === '"' || $char === '\'')
            ? $path.$char
            : $path;
    }
}
