<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\WatcherCommand;

class WatcherCommandTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $command = new WatcherCommand();

        $this->assertInstanceOf(WatcherCommand::class, $command);
    }

    /** @test */
    public function command_line_phpunit_arguments_merged_with_config_arguments()
    {
        $this->markTestIncomplete();
        // mock the config somehow, maybe just overwrite $options?
        // cant, so just manually tweak the real file to test?

        // every method in there is protected, so can't actually test any of it?
            // maybe other parts can be :shrug:

        // should it go here, or in the phpunit screen, or somewhere else?

//        $userOptions = [
//            'notifications' => [
//                'passingTests' => false,
//            ],
//        ];
//
//        $actualOptions = WatcherCommand::create($userOptions)[1];
//
//        $this->assertFalse($actualOptions['notifications']['passingTests']);
//        $this->assertTrue($actualOptions['notifications']['failingTests']);
//        $this->assertFalse($actualOptions['hideManual']);
//        $this->assertSame('*.php', $actualOptions['watch']['fileMask']);
    }
}
