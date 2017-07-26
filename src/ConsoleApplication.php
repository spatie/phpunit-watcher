<?php

namespace Spatie\HttpStatusCheck;

use Symfony\Component\Console\Application;

class ConsoleApplication extends Application
{
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('PHPUnit Watcher', '1.0.0');

        $this->add(new WatcherCommand());
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Spatie</comment>';
    }
}
