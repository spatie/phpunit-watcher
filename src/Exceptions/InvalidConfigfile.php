<?php

namespace Spatie\PhpUnitWatcher\Exceptions;

use Exception;

class InvalidConfigfile extends Exception
{
    public static function invalidContents(string $path)
    {
        return new static("The content of configfile `{$path}` is not valid. Make sure this file contains valid yaml.");
    }
}
