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
 * CRC32 content hash implementation.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Crc32MetaDataHash implements HashInterface
{
    /** @var bool */
    protected $clearStatCache;

    /**
     * Assign option to clear the file stat() cache.
     * @param bool $clearStatCache
     */
    public function __construct($clearStatCache = false)
    {
        $this->clearStatCache = $clearStatCache;
    }

    /**
     * {@inheritdoc}
     */
    public function hash($filepath)
    {
        if ($this->clearStatCache) {
            clearstatcache(true, $filepath);
        }

        $data = stat($filepath);

        $str = basename($filepath) . $data['size'] . $data['mtime'] . $data['mode'];

        return hash('crc32', $str);
    }
}
