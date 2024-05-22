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
 * Interface of a resource cache.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ResourceCacheInterface
{
    /**
     * If the cache Initialized? if not then warm-up cache.
     *
     * @return bool
     */
    public function isInitialized();

    /**
     * Returns the hash of a file in cache.
     *
     * @param string $filename
     *
     * @return string The hash for the filename. Empty string if not exists.
     */
    public function read($filename);

    /**
     * Updates the hash of a file in cache.
     *
     * @param string $filename
     * @param string $hash The calculated hash for the filename.
     */
    public function write($filename, $hash);

    /**
     * Deletes a file in cache.
     *
     * @param string $filename
     *
     * @return void
     */
    public function delete($filename);

    /**
     * Erases all the elements in cache.
     *
     * @return void
     */
    public function erase();

    /**
     * Returns all the element in cache.
     *
     * @return array A key-value array in which the key is the filename and the value is the hash.
     */
    public function getAll();

    /**
     * Persists the cache
     *
     * @return void
     */
    public function save();
}
