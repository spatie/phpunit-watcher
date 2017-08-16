<?php

namespace Spatie\PhpUnitWatcher;

use Spatie\PhpUnitWatcher\PhpUnit\Command as PhpUnitCommand;

class Arguments
{
    /** @var string  */
    protected $filterArgument;
    protected $phpUnitOptions = [];
    protected $applicationOptions = [];

    public static function fromString(string $argumentsInput)
    {
        return (new static)->parse($argumentsInput);
    }

    public function setFilterArgument($filterArgument)
    {
        $this->filterArgument = $filterArgument;

        return $this;
    }

    public function setFilter($query)
    {
        $this->addArgument('--filter', $query, ' ');

        return $this;
    }

    /**
     * Parses a raw arguments string.
     *
     * The following types of arguments will be parsed: space separated arguments, key/value and boolean arguments,
     * and test file name arguments. Space separated arguments are key/value arguments separated by a space. Boolean
     * and key/value arguments are regular options that either take no parameters or take a =-separated parameter.
     * Test file name arguments are file names (without option flags).
     *
     * @param $argumentsInput
     * @return $this
     */
    protected function parse(string $argumentsInput)
    {
        $arguments = explode(' ', $argumentsInput);

        // Keeps track of option name belonging to value when option name and value are space separated
        $nextArgumentBelongsTo = false;

        // PHPUnit only uses first file when multiple are given
        $filterArgumentHasBeenUsed = false;

        foreach ($arguments as $argument) {
            if ($nextArgumentBelongsTo) {
                $this->addArgument($nextArgumentBelongsTo, $argument, ' ');
                $nextArgumentBelongsTo = false;
                continue;
            }

            if ($this->isOption($argument) && $this->isOptionWithSpaceSeparatedArgument($this->optionName($argument))) {
                $nextArgumentBelongsTo = $argument;
                continue;
            }

            if ($this->isOption($argument)) {
                $this->parseOption($argument);
                continue;
            }

            if (! $filterArgumentHasBeenUsed) {
                $this->filterArgument = $argument;
                $filterArgumentHasBeenUsed = true;
            }
        }

        return $this;
    }

    /*
     * Adds an argument if it does not yet exist, otherwise the argument value is replaced.
     *
     * In case $value is left empty, the argument will be treated as a boolean option. If the
     * name and value are not separated by a space, pass the correct separator as a third
     * parameter.
     *
     * Unless an option is registered (in this class) as an application option, we will assume
     * the option is meant to be passed through to PHPUnit.
     */
    public function addArgument(string $name, string $value = null, string $separator = '=')
    {
        $valueWithSeparator = is_null($value) ? $value : ['value' => $value, 'separator' => $separator];

        if ($this->isApplicationOption($this->optionName($name))) {
            $this->applicationOptions[$name] = $valueWithSeparator;

            return;
        }

        $this->phpUnitOptions[$name] = $valueWithSeparator;
    }

    public function phpUnitArguments(): string
    {
        $arguments = array_map(function($name, $optionValue) {
            return is_null($optionValue)
                ? $name
                : $name.$optionValue['separator'].$optionValue['value'];
        }, array_keys($this->phpUnitOptions), $this->phpUnitOptions);


        if (! empty($this->filterArgument)) {
            $arguments[] = $this->filterArgument;
        }

        return implode(' ', $arguments);
    }

    public function toArray(): array
    {
        return array_merge(
            $this->applicationOptions,
            $this->phpUnitOptions,
            empty($this->filterArgument)
                ? []
                : [$this->filterArgument => null]
        );
    }

    protected function parseOption(string $argument)
    {
        if (strpos($argument, '=') !== false) {
            list($name, $value) = explode('=', $argument);

            $this->addArgument($name, $value);

            return;
        }

        $this->addArgument($argument);
    }

    protected function optionName(string $argument): string
    {
        if (substr($argument, 0, 2) == '--') {
            $argument = substr($argument, 2);
        }

        if (substr($argument, 0, 1) == '-') {
            $argument = substr($argument, 1);
        }

        if (strpos($argument, '=') !== false) {
            $argument = substr($argument, 0, strpos($argument, '='));
        }

        return $argument;
    }

    protected function isOption(string $argument): bool
    {
        return substr($argument, 0, 1) == '-';
    }

    protected function isApplicationOption(string $option): bool
    {
        return in_array($option, $this->applicationOptions);
    }

    protected function isOptionWithSpaceSeparatedArgument(string $option): bool
    {
        return in_array($option, PhpUnitCommand::optionsWithArguments());
    }
}
