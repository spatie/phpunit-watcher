<?php

namespace Spatie\PhpUnitWatcher;

use Spatie\PhpUnitWatcher\Exceptions\InvalidConfigfile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputDefinition;

use PHPUnit\TextUI\CliArguments\Builder;
use PHPUnit\TextUI\CliArguments\Mapper;

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

        var_dump(
        	explode( ' ', $options['phpunit']['arguments'] ),
	        explode( ' ', $commandLineArguments ),
        );die();

//        var_dump(
//        	$input->getArguments(),
//            $input->getOptions()
//        );die();

//        $stringinput_config = new StringInput( $options['phpunit']['arguments'] );
//        $stringinput_cli = new StringInput( $commandLineArguments );

//        $commandLineArgumentsArray = explode( ' ', $commandLineArguments );
//        $stringinput_cli->bind( new InputDefinition( $commandLineArgumentsArray ) ); // have to do this before parse() is called?
//
//        var_dump(
////        	$stringinput_config,
////	        $stringinput_cli,       // looks correct, just can't access anything
////        $stringinput_config->__toString() // works
////        $stringinput_cli->getParameterOption(  )  // what to pass here?
//
////        	$stringinput_config->getOptions(),
////	        $stringinput_cli->getOptions()
//        $stringinput_config->getArguments()
//        );die();
//






        $commandLineArgumentsArray = explode( ' ', $commandLineArguments );
        $stringinput_cli->bind( new InputDefinition( $commandLineArgumentsArray ) ); // have to do this before parse() is called?

        var_dump(
//        	$stringinput_config,
//	        $stringinput_cli,       // looks correct, just can't access anything
//        $stringinput_config->__toString() // works
//        $stringinput_cli->getParameterOption(  )  // what to pass here?

//        	$stringinput_config->getOptions(),
//	        $stringinput_cli->getOptions()
        $stringinput_config->getArguments()
        );die();





//        $b = new Builder();
            // document warning that this is internal, no back-compat
	        // rename

//        var_dump(
//        	$options['phpunit']['arguments'],
//        	$GLOBALS['argv'],
//        	$commandLineArguments,
//        	$b->fromParameters( $GLOBALS['argv'], array() ), // gives back an array with ALL options
//            // can't call ^ w/ phpunit.xml.dist b/c not array. can maybe convert to array that mathces though? how does argv split things? just by space?
//                // oh yeah it is just sep by space, so maybe that'll work
//            $b->fromParameters( explode( ' ', $options['phpunit']['arguments'] ), array() ), // gives back an array with ALL options
//
//            // should be passing 2nd arg to fromparams?
//
//        );die();
//

//	    require_once( '/home/api/public_html/vendor/phpunit/phpunit/src/TextUI/CliArguments/Mapper.php' );
//	        // make dynamic? - no, composer should autoload it
//
//	    $obj = $b->fromParameters( explode( ' ', $commandLineArguments ), array() );
//	    $map = new Mapper();
////	    var_dump(
////	    	// $obj
////	        $map->mapToLegacyArray( $obj )
////	    );
////	    die();
//
//	    $fullParams = array_merge_recursive(
//            $map->mapToLegacyArray( $b->fromParameters( explode( ' ', $commandLineArguments ), array() ) ), // just use  $argv instead?
//            $map->mapToLegacyArray( $b->fromParameters( explode( ' ', $options['phpunit']['arguments'] ), array() ) ),
//
//	            // does legacy array contain all the same aprams just in array format?
//	    );
////
//	    var_dump($fullParams);die();    // this does work, but might not be sustainable, and processisolation is array with two entries

        // how to display the options being used if you have the whole array?
	        // just show the strings before being merged?
	        // diff the parsed against the defaults? get defaults by passing empty string

//
//        $parsedCLIArgs = new StringInput( $commandLineArguments );
//
////        var_dump(
//////        	$input->getArguments(),
//////    		$input->getArgument('phpunit-options'),
//////        $parsedCLIArgs->
////
//////        $input->getOptions()
////	    );
//        die();
//

        // check php src for getopt(), see what it does

//	    var_dump(
//	    	strtok( $options, '-' ),
//	    	strtok( $options, '--' ),
//            strtok( $commandLineArguments, '--' ),
//		    preg_split( "/[--]+/", $options ),
//		    $GLOBALS['argv'],
//		    explode( '--', $commandLineArguments )
//	    );
//	    die();
//
	    // use https://github.com/docopt/docopt.php ?
	    // or commando?
	    // doesn't seem like symphone/console can work w/ arbitrary string, which is needed for config


//var_dump(
//	$options['phpunit']['arguments'] ,
////	explode( ) - better built in function like getopt() ?
//	$commandLineArguments,
////$argv,
////$input->getArguments(),
//);


	// maybe support it being an array instead of a string?
	// maintain backcompat though
	    // but then can't accept it as an array from command line input, so still need to parse

        // first get working w/ ./vendor...
	    // then w/ composer run ...

        if (! empty($commandLineArguments)) {
//            $options['phpunit']['arguments'] = array_replace_recursive(
//            	$options['phpunit']['arguments'],
//	            $commandLineArguments
//            );
//            // need to parse them into arrays instead of just strings
            // check if failed?

	        /*
	         * Merge config and command-line arguments.
	         *
	         * Parsing and literally merging the strings would be error-prone. Appending the CLI string to the
	         * config string will result in the config args being treated as defaults, with command args
	         * individually overriding them.
	         *
	         * todo will that build up over time though, so you'll have strings really with tons of redundant options?
	         * if so, then maybe `composer require docopt/docopt.php or nategood/commando or c9s/getoptkit or one of those others from stackoverflow thread
	         *      do those work for this case? maybe not b/c they want you to explicitly define accepted args instead of just parsing whatever you throw at it?
	         *              if ^ then maybe refactor so that screens always have access to original config args, and can append the new ones to that, removing the snowballing
	         * how does phpunit itself handle it, can you reuse that?
	         */
	        $options['phpunit']['arguments'] = $options['phpunit']['arguments'] . ' ' . $commandLineArguments;
        }
//        if (! empty($commandLineArguments)) {
////            $options['phpunit']['arguments'] = array_replace_recursive(
////            	$options['phpunit']['arguments'],
////	            $commandLineArguments
////            );
////            // need to parse them into arrays instead of just strings
//            // check if failed?
//
//	        /*
//	         * Merge config and command-line arguments.
//	         *
//	         * Parsing and literally merging the strings would be error-prone. Appending the CLI string to the
//	         * config string will result in the config args being treated as defaults, with command args
//	         * individually overriding them.
//	         *
//	         * todo will that build up over time though, so you'll have strings really with tons of redundant options?
//	         * if so, then maybe `composer require docopt/docopt.php or nategood/commando or c9s/getoptkit or one of those others from stackoverflow thread
//	         *      do those work for this case? maybe not b/c they want you to explicitly define accepted args instead of just parsing whatever you throw at it?
//	         *              if ^ then maybe refactor so that screens always have access to original config args, and can append the new ones to that, removing the snowballing
//	         * how does phpunit itself handle it, can you reuse that?
//	         */
//	        $options['phpunit']['arguments'] = $options['phpunit']['arguments'] . ' ' . $commandLineArguments;
//        }
//var_dump( $options['phpunit']['arguments'] );
//die();

        if (OS::isOnWindows()) {
            $options['hideManual'] = true;
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
