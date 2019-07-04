<?php

namespace Spatie\PhpUnitWatcher\Test\Screens;

use Clue\React\Stdio\Stdio;
use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\Terminal;
use Spatie\PhpUnitWatcher\Screens\Phpunit;
use React\EventLoop\Factory as LoopFactory;

class PhpunitTest extends TestCase
{
    protected $terminal;
    protected $screen;

    public function setUp()
    {
        $this->terminal = new Terminal(new Stdio(LoopFactory::create()));
        $this->screen = (new Phpunit(['autoFilter' => true]))
            ->useTerminal($this->terminal);
    }

    public function tearDown()
    {
        unset($this->screen, $this->terminal);
    }

    /** @test */
    public function phpunit_determine_autofilter_works_single_file()
    {
        // After draw, $screen::phpunitArguments should include filter for FakeFileTest
        $this->screen->determineAutoFilter(['/this/is/a/test/path/to/a/FakeFile.php']);
        $this->assertObjectHasAttribute('phpunitArguments', $this->screen);

        // Make sure phpunitArguments filters on FakeFileTest
        $this->assertContains(
            '--filter="/(FakeFileTest)/"',
            $this->screen->getPhpunitArguments(),
            'Expected filter was not set');
    }

    /** @test */
    public function phpunit_determine_autofilter_works_multiple_files()
    {
        // After draw, $screen::phpunitArguments should include filter for FakeFileTest
        $this->screen->determineAutoFilter([
            '/this/is/a/test/path/to/a/FakeFile1.php',
            '/test/path/to/a/FakeFile2.php',
        ]);
        $this->assertObjectHasAttribute('phpunitArguments', $this->screen);

        // Make sure phpunitArguments filters on FakeFileTest
        $this->assertContains(
            '--filter="/(FakeFile1Test|FakeFile2Test)/"',
            $this->screen->getPhpunitArguments(),
            'Expected filter was not set');
    }
}
