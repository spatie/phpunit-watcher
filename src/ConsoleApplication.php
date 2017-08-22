<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Console\Application;

class ConsoleApplication extends Application
{
    public function __construct()
    {
        parent::__construct('PHPUnit Watcher');

        $this->add(new WatcherCommand());
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Spatie</comment>';
    }
}
