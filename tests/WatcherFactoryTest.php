<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\WatcherFactory;

class WatcherFactoryTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $factory = new WatcherFactory();

        $this->assertInstanceOf(WatcherFactory::class, $factory);
    }

    /** @test */
    public function setting_notification_preserves_other_options()
    {
        $userOptions = [
            'notifications' => [
                'passingTests' => false,
            ],
        ];

        $actualOptions = WatcherFactory::create($userOptions)[1];

        $this->assertFalse($actualOptions['notifications']['passingTests']);
        $this->assertTrue($actualOptions['notifications']['failingTests']);
        $this->assertFalse($actualOptions['hideManual']);
        $this->assertSame('*.php', $actualOptions['watch']['fileMask']);
    }

    /** @test */
    public function setting_directories_preserves_other_options()
    {
        $userOptions = [
            'watch' => [
                'directories' => ['lib', 'tests'],
            ],
        ];

        $actualOptions = WatcherFactory::create($userOptions)[1];

        $this->assertTrue($actualOptions['notifications']['failingTests']);
        $this->assertFalse($actualOptions['hideManual']);
        $this->assertSame('*.php', $actualOptions['watch']['fileMask']);
    }
}
