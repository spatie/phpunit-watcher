<?php

namespace Spatie\PhpUnitWatcher;

use Spatie\PhpUnitWatcher\WatcherFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class WatcherCommand extends Command
{
    protected function configure()
    {
        $this->setName('watch')
            ->setDescription('Rerun PHPUnit tests when source code changes.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = [];

        $watcher = WatcherFactory::create($config);

        $watcher->startWatching();
    }
}
