<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->determineOptions($input);

        list($watcher, $options) = WatcherFactory::create($options);

        $this->displayOptions($options, $input, $output);

        $watcher->startWatching();
    }

    protected function determineOptions(InputInterface $input): array
    {
        $options = $this->getOptionsFromConfigFile();

        $options['phpunitArguments'] = trim($input->getArgument('phpunit-options'), "'");

        return $options;
    }

    protected function getOptionsFromConfigFile(): array
    {
        $configFile = getcwd().'/.phpunit-watcher.yml';

        if (! file_exists($configFile)) {
            return [];
        }

        return Yaml::parse(file_get_contents($configFile));
    }

    protected function displayOptions(array $options, InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        $output->title('Starting PHPUnit Watcher');

        $output->text("Tests will be rerun when {$options['watch']['fileMask']} files are modified in");

        $output->listing($options['watch']['directories']);

        $output->newLine();
    }
}
