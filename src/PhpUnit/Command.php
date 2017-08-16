<?php

namespace Spatie\PhpUnitWatcher\PhpUnit;

use PHPUnit\TextUI\Command as PhpUnitCommand;

class Command extends PhpUnitCommand
{
    public static function options()
    {
        $options = array_keys((new static)->longOptions);

        return self::removeEqualSigns($options);
    }

    public static function optionsWithArguments()
    {
        $options = array_keys((new static)->longOptions);

        return self::removeEqualSigns(array_filter($options, function ($option) {
            return substr($option, -1) === '=' && substr($option, -2) !== '==';
        }));
    }

    protected static function removeEqualSigns($options)
    {
        return array_map(function ($option) {
            return str_replace('=', '', $option);
        }, $options);
    }
}
