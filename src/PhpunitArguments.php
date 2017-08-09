<?php

namespace Spatie\PhpUnitWatcher;

class PhpunitArguments
{
    protected $arguments;

    public static function fromString($argumentsInput)
    {
        $phpunitArguments = new PhpunitArguments;

        $arguments = explode(' ', $argumentsInput);

        foreach ($arguments as $argument) {
            if (strpos($argument, '=') !== false) {
                list($name, $value) = explode('=', $argument);
                $phpunitArguments->addArgument($name, $value);

                continue;
            }
            $phpunitArguments->addArgument($argument);
        }

        return $phpunitArguments;
    }

    public function addArgument($name, $value = null)
    {
        $this->arguments[$name] = $value;
    }

    public function toArray()
    {
        return $this->arguments;
    }

    public function toString()
    {
        $arguments = [];

        foreach ($this->arguments as $name=>$value) {
            if (is_null($value)) {
                $arguments[] = $name;
                continue;
            }
            $arguments[] = $name.'='.$value;
        }

        return implode(' ', $arguments);
    }
}