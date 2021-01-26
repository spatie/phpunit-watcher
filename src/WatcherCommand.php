<?php

namespace Spatie\PhpUnitWatcher;

use Spatie\PhpUnitWatcher\Exceptions\InvalidConfigfile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

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

        [$watcher, $options] = WatcherFactory::create($options);

        $this->displayOptions($options, $input, $output);

        $watcher->startWatching();
    }

    protected function determineOptions(InputInterface $input): array
    {
        $options = $this->getOptionsFromConfigFile();

        $commandLineArguments = trim($input->getArgument('phpunit-options'), "'");

        $options['phpunit']['arguments'] = $this->mergePHPUnitArguments($options['phpunit']['arguments'], $commandLineArguments);

        if (OS::isOnWindows()) {
            $options['hideManual'] = true;
        }

        return $options;
    }

    // credit: modified version of https://stackoverflow.com/a/65891967/450127
    // https://stackoverflow.com/users/1889685/christos-lytras
    // license CC BY-SA 4.0 -- https://creativecommons.org/licenses/by-sa/4.0/
    public static function mergePHPUnitArguments($config_params, $runtime_params)
    {
        $config_params = explode(' ', $config_params);
        $runtime_params = explode(' ', $runtime_params);

        // todo test edge case where config has "--var foo" and the other has "--var=foo"
        // should accept and merge, but probably not critical
        // cover this case in unit tests

        // todo also messes up with longopts with dashes, e.g. `--process-isolation`
        // regex needs to account for that
        // cover that case in unit tests

        // clean all this up

        // Merge all parameters, CLI arguments and from config
        $all_params = array_merge($config_params, $runtime_params);

        // We'll save all the params here using assoc array
        // to identify and handle/override duplicate commands
        $params = [];

        foreach ($all_params as $param) {
            // This regex will match everything:
            // -d
            // xdebug.mode=off
            // --columns=95
            // and create 4 groups:
            // 1: the pre char(s), - or --
            // 2: the cmd, actual command
            // 3: the eq char, =
            // 4: the value
            if (preg_match('/^(-[-]?)?([\w.]+)(=?)(.*)/', $param, $matches)) {
                // Destructure matches
                [ , $pre, $cmd, $eq, $value ] = $matches;
                $param = [
                    'pre' => $pre,
                    'cmd' => $cmd,
                    'eq' => $eq,
                    'value' => $value,
                ];

                // If the command is set, merge it with the previous,
                // else add it to $params array
                if (isset($params[ $cmd ])) {
                    $params[ $cmd ] = array_merge($params[ $cmd ], $param);
                } else {
                    $params[ $cmd ] = $param;
                }
            }
        }

        $merged = [];

        // Loop through all unique params and re-build the commands
        foreach ($params as $param) {
            [
                'pre' => $pre,
                'cmd' => $cmd,
                'eq' => $eq,
                'value' => $value,
            ] = $param;

            if (! empty($pre)) {
                $cmd = $pre . $cmd;
            }

            if (! empty($eq)) {
                $cmd .= $eq;

                if (! empty($value)) {
                    $cmd .= $value;
                }
            }

            $merged[] = $cmd;
        }

        return implode(' ', $merged);
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
                $configFullPath = $configDirectory.DIRECTORY_SEPARATOR.$configName;

                if (file_exists($configFullPath)) {
                    return $configFullPath;
                }
            }

            $parentDirectory = dirname($configDirectory);

            // We do a direct comparison here since there's a difference between
            // the root directories on windows / *nix systems which does not
            // let us compare it against the DIRECTORY_SEPARATOR directly
            if ($parentDirectory === $configDirectory) {
                return;
            }

            $configDirectory = $parentDirectory;
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
