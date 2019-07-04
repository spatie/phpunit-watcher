<?php

namespace Spatie\PhpUnitWatcher;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption(
                'auto-filter',
                'a',
                InputOption::VALUE_NONE,
                'Only run tests corresponding to edited files.')
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

        $options['autoFilter'] = $input->getOption('auto-filter');

        $commandLineArguments = trim($input->getArgument('phpunit-options'), "'");

        if (! empty($commandLineArguments)) {
            $options['phpunit']['arguments'] = $commandLineArguments;
        }

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
        $configNames = [
            '.phpunit-watcher.yml',
            'phpunit-watcher.yml',
            'phpunit-watcher.yml.dist',
        ];

        $configDirectory = getcwd();

        while (is_dir($configDirectory)) {
            foreach ($configNames as $configName) {
                $configFullPath = "{$configDirectory}/{$configName}";

                if (file_exists($configFullPath)) {
                    return $configFullPath;
                }
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

        $output->text("Tests will be rerun when {$options['watch']['fileMask']} files are modified in");

        $output->listing($options['watch']['directories']);

        $output->newLine();

        if ($options['autoFilter']) {
            $output->text('Tests will be filtered corresponding to the changed filename.');
            $output->text('For example, editing BlogService.php will filter for BlogServiceTest.');
            $output->newLine();
        }
    }
}
