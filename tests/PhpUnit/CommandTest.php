<?php

namespace Spatie\PhpUnitWatcher\Test\PhpUnit;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\PhpUnit\Command;

class CommandTest extends TestCase
{
    /** @test */
    public function options_can_be_retrieved()
    {
        $arguments = Command::options();

        $this->assertContains('help', $arguments);
        $this->assertContains('testdox', $arguments);
    }
    
    /** @test */
    public function option_names_with_arguments_are_converted_into_regular_option_names()
    {
        $arguments = Command::options();

        $this->assertContains('colors', $arguments);
        $this->assertContains('coverage-xml', $arguments);
        $this->assertContains('filter', $arguments);
    }

    /** @test */
    public function options_that_take_arguments_separated_by_spaces_can_be_retrieved()
    {
        $options = Command::optionsWithArguments();

        $this->assertContains('coverage-xml', $options);
        $this->assertContains('filter', $options);
        $this->assertNotContains('help', $options);
        $this->assertNotContains('testdox', $options);
        $this->assertNotContains('colors', $options);
    }
}
