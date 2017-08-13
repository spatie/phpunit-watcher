<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Spatie\PhpUnitWatcher\Exceptions\InvalidConfigfile;

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

        $commandLineArguments = trim($input->getArgument('phpunit-options'), "'");

        $options['phpunit']['arguments'] = Arguments::fromString($commandLineArguments);

        return $options;
    }

    protected function getOptionsFromConfigFile(): array
    {
        $configFilePath = $this->getConfigFileLocation();

        if (! file_exists($configFilePath)) {
            return [];
        }

        $options = Yaml::parse(file_get_contents($configFilePath));

        if (is_null($options)) {
            throw InvalidConfigfile::invalidContents($configFilePath);
        }

        $options['configFilePath'] = $configFilePath;

        return $options;
    }

    protected function getConfigFileLocation()
    {
        $configName = '.phpunit-watcher.yml';

        $configDirectory = getcwd();

        while (is_dir($configDirectory)) {
            $configFullPath = "{$configDirectory}/{$configName}";

            if (file_exists($configFullPath)) {
                return $configFullPath;
            }

            if ($configDirectory === DIRECTORY_SEPARATOR) {
                return;
            }
            $configDirectory = dirname($configDirectory);
        }
    }

    protected function displayOptions(array $options, InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        $output->title('PHPUnit Watcher');

        $output->text("PHPUnit Watcher {$this->getApplication()->getVersion()} by Spatie and contributors.");
        $output->newLine();

        if (isset($options['configFilePath'])) {
            $output->text("Using options from configfile at `{$options['configFilePath']}`");
        } else {
            $output->text('No config file detected. Using default options.');
        }
        $output->newLine();

        $output->text("Tests will be rerun when {$options['watch']['fileMask']} files are modified in");

        $output->listing($options['watch']['directories']);

        $output->newLine();
    }
}
