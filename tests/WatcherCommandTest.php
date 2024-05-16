<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\WatcherCommand;

class WatcherCommandTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated()
    {
        $command = new WatcherCommand();

        $this->assertInstanceOf(WatcherCommand::class, $command);
    }
}
