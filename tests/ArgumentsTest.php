<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\Arguments;

class ArgumentsTest extends TestCase
{
    /** @test */
    public function arguments_string_can_be_parsed()
    {
        $arguments = Arguments::fromString('--filter the_name_of_the_test --colors=never -h --start-watching=immediately --stop-on-failure');

        $this->assertEquals([
            '--filter' => ['value' => 'the_name_of_the_test', 'separator' => ' '],
            '--colors' => ['value' => 'never', 'separator' => '='],
            '-h' => null,
            '--start-watching' => ['value' => 'immediately', 'separator' => '='],
            '--stop-on-failure' => null,
        ], $arguments->toArray());
    }

    /** @test */
    public function phpunit_arguments_remain_semantically_equal_after_parsing_from_and_to_string()
    {
        $arguments = Arguments::fromString('filename --colors=never -h --filter filter_on_this.php something_else --stop-on-failure');

        $this->assertEquals('--colors=never -h --filter filter_on_this.php --stop-on-failure filename', $arguments->phpUnitArguments());
    }
}
