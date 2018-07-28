<?php

namespace Spatie\PhpUnitWatcher\Screens\Completers;

use Clue\React\Stdio\Readline;

abstract class Completer
{
    /**
     * @var \Clue\React\Stdio\Readline
     */
    protected $readline;

    /**
     * @var string
     */
    protected $word;

    /**
     * @var int
     */
    protected $wordStartOffset;

    /**
     * @var int
     */
    protected $wordEndOffset;

    /**
     * @var \Closure|callable
     */
    protected $filterSuggestionsCallback;

    /**
     * @param \Clue\React\Stdio\Readline $readline
     */
    public function __construct(Readline $readline)
    {
        $this->readline = $readline;
    }

    /**
     * Start completion
     *
     * @param string $word
     * @param int $startOffset
     * @param int $endOffset
     * @return array|null
     */
    public function __invoke($word, $startOffset, $endOffset)
    {
        $this->word = $word;
        $this->wordStartOffset = $startOffset;
        $this->wordEndOffset = $endOffset;

        return $this->handleSearchResults(
            $this->search()
        );
    }

    /**
     * Search for things related to the completed word and return an array of suggestions
     *
     * Use an array with one element to autocomplete the word
     *
     * @return array
     */
    abstract protected function search();

    /**
     * Display suggestions or complete the word depending on number of results
     *
     * When returning an array reactphp-stdio will display suggestions with the default behavior.
     *
     * @param array $searchResults
     * @return array|null
     */
    protected function handleSearchResults($searchResults)
    {
        if (empty($searchResults)) {
            return null;
        }

        if (count($searchResults) > 1) {
            return $this->filterSuggestions($searchResults);
        }

        $this->completeWord($searchResults[0]);
    }

    /**
     * Filter suggestions to display
     *
     * @param array $suggestions
     * @return array|null
     */
    protected function filterSuggestions($suggestions)
    {
        if (is_callable($this->filterSuggestionsCallback)) {
            $suggestions = ($this->filterSuggestionsCallback)($suggestions);
        }
        return $suggestions;
    }

    /**
     * Register callback called when more than one suggestion is available
     *
     * You can use this callback to filter suggestions or to abort the
     * default display behavior by returning null
     *
     * @param callable $callback
     */
    public function onSuggestions($callback)
    {
        $this->filterSuggestionsCallback = $callback;
    }

    /**
     * Refresh the input with the completed word and move cursor to end of the word
     *
     * @param string $newWord
     */
    protected function completeWord($newWord)
    {
        $endQuotedWord = $this->appendQuoteIfNeeded($newWord);

        $this->readline->setInput(
            $this->getInputBeforeWord() . $endQuotedWord . $this->getInputAfterWord()
        );

        $this->readline->moveCursorTo($this->wordStartOffset + mb_strlen($endQuotedWord));
    }

    /**
     * Return input string before the word
     *
     * @return string
     */
    protected function getInputBeforeWord()
    {
        return mb_substr($this->getInput(), 0, $this->wordStartOffset);
    }

    /**
     * Return input string after the word
     *
     * @return string
     */
    protected function getInputAfterWord()
    {
        return mb_substr($this->getInput(), $this->wordEndOffset);
    }

    /**
     * Return the character before the word
     *
     * @return null|string
     */
    protected function getCharBeforeWord()
    {
        return $this->wordStartOffset > 0
            ? mb_substr($this->getInput(), $this->wordStartOffset - 1, 1)
            : null;
    }

    /**
     * Append a quote if word is prefixed with a quote
     *
     * @param string $word
     * @return string
     */
    protected function appendQuoteIfNeeded($word)
    {
        $char = $this->getCharBeforeWord();

        return ($char === '"' || $char === '\'')
            ? $word . $char
            : $word;
    }

    /**
     * Return the input string
     *
     * @return string
     */
    protected function getInput()
    {
        return $this->readline->getInput();
    }
}
