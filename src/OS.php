<?php

namespace Spatie\PhpUnitWatcher;

class OS
{
    /**
     * Indicates if the process is being executed in a windows machine.
     *
     * @return bool
     */
    public static function isOnWindows()
    {
        return DIRECTORY_SEPARATOR !== '/';
    }
}
