<?php

/*
 * This file is part of the Yo! Symfony Resource Watcher.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spatie\PhpUnitWatcher\ResourceWatcher\Tests;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\ResourceWatcher\Crc32MetaDataHash;

class Crc32MetaDataHashTest extends TestCase
{
    public function testHashMustReturnTheMetaDataDigestWithCRC32()
    {
        $filepath = sys_get_temp_dir() . '/test.txt';

        touch($filepath, strtotime('2020-05-25 17:42'));

        $crc32ContentHash = new Crc32MetaDataHash();
        $currentValue = $crc32ContentHash->hash($filepath);

        $fileData = stat($filepath);

        $expected = basename($filepath) . $fileData['size'] . $fileData['mtime'] . $fileData['mode'];

        $this->assertEquals(hash('crc32', $expected), $currentValue);

        unlink($filepath);
    }
}
