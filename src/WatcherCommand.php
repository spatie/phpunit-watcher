<?php

namespace Spatie\PhpUnitWatcher;

use Spatie\PhpUnitWatcher\Exceptions\InvalidConfigfile;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatcherCommand extends Command
{
    const PHPUNIT_WATCH_CONFIG_FILENAME = '/.phpunit-watcher.yml';

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
        $configFile = $this->getConfigFileLocation();

        if (! file_exists($configFile)) {
            return [];
        }

        $options = Yaml::parse(file_get_contents($configFile));

        if (is_null($options)) {
            throw InvalidConfigfile::invalidContents($configFile);
        }

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

        $output->text("Tests will be rerun when {$options['watch']['fileMask']} files are modified in");

        $output->listing($options['watch']['directories']);

        $output->newLine();
    }
}
