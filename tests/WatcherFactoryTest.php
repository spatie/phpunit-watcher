<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\WatcherFactory;

class WatcherFactorTest extends TestCase
{
	public function setUp() {
		parent::setUp();

		// todo need to create foo and bar folders inside current working dir
	}

	public function tearDown() {
		parent::tearDown();

		// todo revert setUp();
	}

	/** @test */
    public function it_can_be_instantiated()
    {
        $factory = new WatcherFactory();

        $this->assertInstanceOf(WatcherFactory::class, $factory);
    }

    /**
     * @test
     *
     * @covers WatcherFactory::create
     *
     * @dataProvider data_options_are_replaced_recursively
     */
	public function options_are_replaced_recursively( $userOptions, $expectedOptions ) {
		$watcher = WatcherFactory::create( $userOptions );
		$actualOptions = $watcher[1];

		$this->assertEquals( $expectedOptions, $actualOptions );
	}

	public function data_options_are_replaced_recursively() {
		$cases = array();
		$defaults = WatcherFactory::getDefaultOptions();

		// Setting watch.directories should leave all other defaults in tact.
		$cases['overwriteDirectoriesButNotMask'] = array(
    	    array(
    	    	'watch' => array(
	                'directories' => array( 'foo', 'bar' )
		        ),
            ),
			$defaults
		);
    	$cases['overwriteDirectoriesButNotMask'][1]['watch']['directories'] = array( 'foo', 'bar' );

    	return $cases;
	}
}
