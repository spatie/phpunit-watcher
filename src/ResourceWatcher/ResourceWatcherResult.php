<?php

/*
 * This file is part of the Yo! Symfony Resource Watcher.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spatie\PhpUnitWatcher\ResourceWatcher;

/**
 * A Resource Watcher result with the filesystem changes.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResourceWatcherResult
{
    private $hasChanges;
    private $newResources;
    private $deletedResources;
    private $updatedResources;

    /**
     * Constructor.
     */
    public function __construct(array $newResources, array $deletedResources, array $updatedResources)
    {
        $this->newResources = $newResources;
        $this->deletedResources = $deletedResources;
        $this->updatedResources = $updatedResources;
    }

    /**
     * Has any change in resources?
     *
     * @return bool
     */
    public function hasChanges()
    {
        if ($this->hasChanges) {
            return $this->hasChanges;
        }

        $this->hasChanges = count($this->newResources) > 0 || count($this->deletedResources) > 0 || count($this->updatedResources) > 0;

        return $this->hasChanges;
    }

    /**
     * Returns an array with paths of the new resources ('.', '..' not resolved).
     *
     * @return array
     */
    public function getNewFiles()
    {
        return $this->newResources;
    }

    /**
     * Returns an array with path of the deleted resources ('.', '..' not resolved).
     *
     * @return array
     */
    public function getDeletedFiles()
    {
        return $this->deletedResources;
    }

    /**
     * Returns an array with path of the updated resources ('.', '..' not resolved).
     *
     * @return array
     */
    public function getUpdatedFiles()
    {
        return $this->updatedResources;
    }
}
