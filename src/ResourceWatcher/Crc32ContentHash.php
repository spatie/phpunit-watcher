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
class Crc32ContentHash implements HashInterface
{
    /**
     * {@inheritdoc}
     */
    public function hash($filepath)
    {
        $fileContent = $filepath;

        if (! \is_dir($filepath)) {
            $fileContent = file_get_contents($filepath);
        }

        return hash('crc32', $fileContent);
    }
}
