<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\PhpunitArguments;

class PhpunitArgumentsTest extends TestCase
{
    /** @test */
    public function arguments_string_can_be_parsed()
    {
        $arguments = PhpunitArguments::fromString("--filter=the_name_of_the_test --stop-on-failure");

        $this->assertEquals([
            '--filter' => 'the_name_of_the_test',
            '--stop-on-failure' => null,
        ], $arguments->toArray());
    }

    /** @test */
    public function arguments_remain_unchanged_after_parsing_from_and_to_string()
    {
        $arguments = PhpunitArguments::fromString("filename --filter=a_filter something_else --stop-on-failure");

        $this->assertEquals('filename --filter=a_filter something_else --stop-on-failure', $arguments->toString());
    }
}
