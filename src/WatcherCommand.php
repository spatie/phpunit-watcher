<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatcherCommand extends Command
{
    protected function configure()
    {
        $this->setName('watch')
            ->setDescription('Rerun PHPUnit tests when source code changes.')
            ->addArgument('phpunit-options', InputArgument::OPTIONAL, 'Options passed to phpunit');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->determineOptions($input);

        $watcher = WatcherFactory::create($options);

        $watcher->startWatching();
    }

    protected function determineOptions(InputInterface $input): array
    {
        $config['phpunitArguments'] = trim($input->getArgument('phpunit-options'), "'");

        return $config;
    }
}
