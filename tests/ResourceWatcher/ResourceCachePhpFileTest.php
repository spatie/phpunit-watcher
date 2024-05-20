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
use Symfony\Component\Filesystem\Filesystem;
use Spatie\PhpUnitWatcher\ResourceWatcher\ResourceCachePhpFile;

class ResourceCachePhpFileTest extends TestCase
{
    private $cacheFile;
    private $fs;
    private $tmpDir;

    public function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/resource-watchers-tests';
        $this->cacheFile = $this->tmpDir . '/cache-file-test.php';
        $this->fs = new Filesystem();
        $this->fs->mkdir($this->tmpDir);
    }

    public function tearDown(): void
    {
        $this->fs->remove($this->tmpDir);
    }

    public function testIsInitializedMustReturnFalseWhenTheCacheFileIsNotExists()
    {
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);

        $this->assertFalse($resourceCache->isInitialized());
    }

    public function testIsInitializedMustReturnTrueWhenTheCacheIsSavedInTheCacheFile()
    {
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);
        $resourceCache->save();

        $this->assertTrue($resourceCache->isInitialized());
    }

    public function testIsInitializedMustReturnTrueWhenThereIsAValidCacheFile()
    {
        $this->fs->dumpFile($this->cacheFile, "<?php\nreturn [];");
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);

        $this->assertTrue($resourceCache->isInitialized());
    }

    public function testSaveMustDumpTheContentCacheInAFile()
    {
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);
        $filename = 'file1.md';
        $hash = '23998C';
        $resourceCache->write($filename, $hash);
        $resourceCache->save();

        $fileContent = file_get_contents($this->cacheFile);

        $this->assertEquals($fileContent, "<?php\nreturn ['$filename'=>'$hash',];");
    }

    public function testConstructWithAInvalidCacheFileMustThrownAnException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache file invalid format.');
        $this->fs->dumpFile($this->cacheFile, '');

        $rc = new ResourceCachePhpFile($this->cacheFile);
    }

    public function testConstructWithANoPhpFileExtensionMustThrownAnException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The cache filename must ends with the extension ".php".');
        $rc = new ResourceCachePhpFile($this->tmpDir . '/cache-file-test.txt');
    }
}
