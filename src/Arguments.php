<?php

namespace Spatie\PhpUnitWatcher;

use Spatie\PhpUnitWatcher\PhpUnit\Command as PhpUnitCommand;

class Arguments
{
    use ArgumentAccessors;

    const APPLICATION_OPTIONS = [];

    protected $testFile;
    protected $phpUnitOptions = [];
    protected $applicationOptions = [];

    /**
     * Parses the given arguments string and instantiates a new object containing the arguments.
     *
     * @param  string $argumentsInput
     * @return $this
     */
    public static function fromString($argumentsInput)
    {
        return (new Arguments)->parse($argumentsInput);
    }

    /**
     * Adds an argument if it does not yet exist, otherwise the argument value is replaced.
     *
     * In case $value is left empty, the argument will be treated as a boolean option. If the
     * name and value are not separated by a space, pass the correct separator as a third
     * parameter.
     *
     * Unless an option is registered (in this class) as an application option, we will assume
     * the option is meant to be passed through to PHPUnit.
     *
     * @param  string $name
     * @param  mixed $value
     * @param  string $separator
     */
    public function addArgument($name, $value = null, $separator = '=')
    {
        $valueWithSeparator = is_null($value) ? $value : ['value' => $value, 'separator' => $separator];

        if ($this->isApplicationOption($this->optionName($name))) {
            $this->applicationOptions[$name] = $valueWithSeparator;
            return;
        }

        $this->phpUnitOptions[$name] = $valueWithSeparator;
    }

    /**
     * Returns a string containing all PHPUnit options with corresponding values in the correct format.
     *
     * @return string
     */
    public function phpUnitArguments()
    {
        $arguments = [];

        foreach ($this->phpUnitOptions as $name => $optionValue) {
            if (is_null($optionValue)) {
                $arguments[] = $name;
                continue;
            }
            $arguments[] = $name.$optionValue['separator'].$optionValue['value'];
        }

        if (!empty($this->testFile)) {
            $arguments[] = $this->testFile;
        }

        return implode(' ', $arguments);
    }

    /**
     * Returns an array representation containing application options, PHPUnit options and the test file,
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->applicationOptions,
            $this->phpUnitOptions,
            empty($this->testFile) ? [] : [$this->testFile => null]
        );
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
    protected function parse($argumentsInput)
    {
        $arguments = explode(' ', $argumentsInput);

        // Keeps track of option name belonging to value when option name and value are space separated
        $nextArgumentBelongsTo = false;

        // PHPUnit only uses first file when multiple are given
        $fileSet = false;

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

            if (!$fileSet) {
                $this->testFile = $argument;
                $fileSet = true;
            }
        }

        return $this;
    }

    /**
     * Parses an argument string of an option (either key/value or boolean) and adds it to the list of arguments.
     *
     * @param  string  $argument
     */
    protected function parseOption($argument)
    {
        if (strpos($argument, '=') !== false) {
            list($name, $value) = explode('=', $argument);

            $this->addArgument($name, $value);

            return;
        }

        $this->addArgument($argument);
    }

    /**
     * Converts an argument string into the name of the option.
     *
     * @param  string  $argument
     * @return mixed
     */
    protected function optionName($argument)
    {
        // String starts with --
        if (substr($argument, 0, 2) == '--') {
            $argument = substr($argument, 2);
        }

        // String starts with -
        if (substr($argument, 0, 1) == '-') {
            $argument = substr($argument, 1);
        }

        // Remove everything after (and including) equals sign
        if (strpos($argument, '=') !== false) {
            $argument = substr($argument, 0, strpos($argument, '='));
        }

        return $argument;
    }

    /**
     * Determines if the given argument string is an option.
     *
     * @param  string  $argument
     * @return bool
     */
    protected function isOption($argument)
    {
        return substr($argument, 0, 1) == '-';
    }

    /**
     * Determines if the given option name is an application option.
     *
     * @param  string  $option
     * @return bool
     */
    protected function isApplicationOption($option)
    {
        return in_array($option, self::APPLICATION_OPTIONS);
    }

    /**
     * Determines if the given option name belongs to an option that takes a space separated argument.
     *
     * @param  string  $option
     * @return bool
     */
    protected function isOptionWithSpaceSeparatedArgument($option)
    {
        return in_array($option, PhpUnitCommand::optionsWithArguments());
    }
}