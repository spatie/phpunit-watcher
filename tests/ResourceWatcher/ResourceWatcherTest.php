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
use Spatie\PhpUnitWatcher\ResourceWatcher\Crc32ContentHash;
use Spatie\PhpUnitWatcher\ResourceWatcher\ResourceCacheMemory;
use Spatie\PhpUnitWatcher\ResourceWatcher\ResourceWatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ResourceWatcherTest extends TestCase
{
    protected $tmpDir;
    protected $fs;

    public function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/resourceWatcher-tests';
        $this->fs = new Filesystem();

        $this->fs->mkdir($this->tmpDir);
    }

    public function tearDown(): void
    {
        $this->fs->remove($this->tmpDir);
    }

    public function testInitializeMustWarmUpTheCacheInCaseItIsCold()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        $cacheMemory = new ResourceCacheMemory();
        $contentHashCrc32 = new Crc32ContentHash();
        $resourceWatcher = new ResourceWatcher($cacheMemory, $finder, $contentHashCrc32);
        $resourceWatcher->initialize();

        $this->assertTrue($cacheMemory->isInitialized());
    }

    public function testHasChangesMustReturnFalseWithColdCache()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        $resourceWatcher = $this->makeResourceWatcher($finder);

        $result = $resourceWatcher->findChanges();

        $this->assertFalse($result->hasChanges());
    }

    public function testHasChangesMustReturnTrueWhenNewFile()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        $resourceWatcher = $this->makeResourceWatcher($finder);

        $resourceWatcher->findChanges();
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        $result = $resourceWatcher->findChanges();

        $this->assertTrue($result->hasChanges());
    }

    public function testHasChangesMustReturnFalseAfterRebuildCache()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        $resourceWatcher = $this->makeResourceWatcher($finder);

        $resourceWatcher->findChanges();
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        $resourceWatcher->rebuild();
        $result = $resourceWatcher->findChanges();

        $this->assertFalse($result->hasChanges());
    }

    public function testFindChangesMustReturnANewFileWhenItIsCreated()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        $resourceWatcher = $this->makeResourceWatcher($finder);

        $resourceWatcher->findChanges();
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        $result = $resourceWatcher->findChanges();

        $this->assertCount(1, $result->getNewFiles());
    }

    public function testFindChangesMustReturnADeletedFileWhenItIsDeleted()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        $resourceWatcher = $this->makeResourceWatcher($finder);

        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        $resourceWatcher->findChanges();
        $this->fs->remove($this->tmpDir . '/file1.txt');
        $result = $resourceWatcher->findChanges();

        $this->assertCount(1, $result->getDeletedFiles());
    }

    public function testFindChangesMustReturnAUpdatedFileWhenItIsModified()
    {
        $filename = $this->tmpDir . '/file1.txt';
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        $resourceWatcher = $this->makeResourceWatcher($finder);

        $this->fs->dumpFile($filename, 'test');
        $resourceWatcher->findChanges();
        @file_put_contents($filename, 'update1', \FILE_APPEND);
        $result = $resourceWatcher->findChanges();

        $this->assertCount(1, $result->getUpdatedFiles());
    }

    public function testFindChangesMustReturnANewFileWhenANewDirectoryIsCreated()
    {
        $finder = new Finder();
        $finder->in($this->tmpDir);
        $resourceWatcher = $this->makeResourceWatcher($finder);

        $resourceWatcher->findChanges();
        $this->fs->mkdir($this->tmpDir . '/dir-test');
        $result = $resourceWatcher->findChanges();
        $newFiles = $result->getNewFiles();

        $this->assertCount(1, $newFiles);
        $this->assertEquals($this->tmpDir . '/dir-test', $newFiles[0]);
    }

    public function testFindChangesMustUsesTheRelativePathWithTheCacheWhenEnableRelativePath()
    {
        $filename = 'file1.txt';
        $file = $this->tmpDir . '/'.$filename;
        $cacheMemory = new ResourceCacheMemory();
        $contentHashCrc32 = new Crc32ContentHash();
        $this->fs->dumpFile($file, 'test');
        $finder = new Finder();
        $finder->in($this->tmpDir);
        $finder->files();

        $resourceWatcher = new ResourceWatcher($cacheMemory, $finder, $contentHashCrc32);
        $resourceWatcher->enableRelativePathWithCache();
        $resourceWatcher->initialize();

        $this->assertTrue(\strlen($cacheMemory->read($filename)) > 0);
        $this->assertTrue(\strlen($cacheMemory->read($file)) == 0);
    }

    public function testFindChangesMustUsesThePathWithTheCacheWhenIsNotEnabledTheRelativePath()
    {
        $filename = 'file1.txt';
        $file = $this->tmpDir . '/'.$filename;
        $cacheMemory = new ResourceCacheMemory();
        $contentHashCrc32 = new Crc32ContentHash();
        $this->fs->dumpFile($file, 'test');
        $finder = new Finder();
        $finder->in($this->tmpDir);
        $finder->files();

        $resourceWatcher = new ResourceWatcher($cacheMemory, $finder, $contentHashCrc32);
        $resourceWatcher->initialize();

        $this->assertTrue(\strlen($cacheMemory->read($filename)) == 0);
        $this->assertTrue(\strlen($cacheMemory->read($file)) > 0);
    }

    private function makeResourceWatcher(Finder $finder)
    {
        $cacheMemory = new ResourceCacheMemory();
        $contentHashCrc32 = new Crc32ContentHash();

        return new ResourceWatcher($cacheMemory, $finder, $contentHashCrc32);
    }
}
