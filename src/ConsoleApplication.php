<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Console\Application;

class ConsoleApplication extends Application
{
    public function __construct()
    {
        parent::__construct('PHPUnit Watcher', '1.23.2');

        $this->add(new WatcherCommand());
    }

    public function getLongVersion(): string
    {
        return parent::getLongVersion().' by <comment>Spatie</comment>';
    }
}
