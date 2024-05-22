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
use Spatie\PhpUnitWatcher\ResourceWatcher\ResourceWatcherResult;

class ResourceWatcherResultTest extends TestCase
{
    private $newFiles;
    private $deletedFiles;
    private $updatedFiles;
    private $resourceWatcherResult;

    public function setUp(): void
    {
        $this->newFiles = ['/my-path/newfile.txt'];
        $this->deletedFiles = ['/my-path/deletedfile.txt'];
        $this->updatedFiles = ['/my-path/updatedfile.txt'];
        $this->resourceWatcherResult = new ResourceWatcherResult(
            $this->newFiles,
            $this->deletedFiles,
            $this->updatedFiles
        );
    }

    public function testHasChangesMustReturnTrueWhenExistsNewFiles()
    {
        $resourceWatcherResult = new ResourceWatcherResult($this->newFiles, [], []);

        $this->assertTrue($resourceWatcherResult->hasChanges());
    }

    public function testHasChangesMustReturnTrueWhenExistsDeletedFiles()
    {
        $resourceWatcherResult = new ResourceWatcherResult([], $this->deletedFiles, []);

        $this->assertTrue($resourceWatcherResult->hasChanges());
    }

    public function testHasChangesMustReturnTrueWhenExistsUpdatedFiles()
    {
        $resourceWatcherResult = new ResourceWatcherResult([], [], $this->updatedFiles);

        $this->assertTrue($resourceWatcherResult->hasChanges());
    }

    public function testHasChangesMustReturnFalseWhenDoesNotExistsChanges()
    {
        $resourceWatcherResult = new ResourceWatcherResult([], [], []);

        $this->assertFalse($resourceWatcherResult->hasChanges());
    }

    public function testGetNewFilesMustReturnTheNewFiles()
    {
        $files = $this->resourceWatcherResult->getNewFiles();
        $this->assertCount(1, $files);
        $this->assertEquals('/my-path/newfile.txt', $files[0]);
    }

    public function testGetDeletedFilesMustReturnTheDeletedFiles()
    {
        $files = $this->resourceWatcherResult->getDeletedFiles();
        $this->assertCount(1, $files);
        $this->assertEquals('/my-path/deletedfile.txt', $files[0]);
    }

    public function testGetUpdatedFilesMustReturnTheDeletedFiles()
    {
        $files = $this->resourceWatcherResult->getUpdatedFiles();
        $this->assertCount(1, $files);
        $this->assertEquals('/my-path/updatedfile.txt', $files[0]);
    }
}
