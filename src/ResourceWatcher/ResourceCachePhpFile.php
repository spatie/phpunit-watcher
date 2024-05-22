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
 * Resource cache implementation using a PHP file with an array.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResourceCachePhpFile extends ResourceCacheMemory
{
    protected $filename;
    protected $hasPendingChasges = false;

    /**
     * Constructor.
     *
     * @param string $filename The cache ".PHP" file. E.g: "resource-watcher-cache.php"
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->warmUpCacheFromFile($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public function write($filename, $hash)
    {
        if ($hash === $this->read($filename)) {
            return;
        }

        parent::write($filename, $hash);

        $this->hasPendingChasges = true;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        if ($this->hasPendingChasges === false) {
            return;
        }

        $content = $this->composeContentCacheFile($this->getAll());

        if (@file_put_contents($this->filename, $content) === false) {
            throw new \RuntimeException(sprintf('Failed to write the cache file "%s".', $this->filename));
        }

        $this->hasPendingChasges = false;
        $this->isInitialized = true;
    }

    /**
     * @param string $filename
     *
     * @return void
     */
    private function warmUpCacheFromFile($filename)
    {
        if (preg_match('#\.php$#', $filename) == false) {
            throw new \InvalidArgumentException('The cache filename must ends with the extension ".php".');
        }

        if (file_exists($filename) == false) {
            $this->hasPendingChasges = true;

            return;
        }

        $fileContent = include($filename);

        if (is_array($fileContent) == false) {
            throw new \InvalidArgumentException('Cache file invalid format.');
        }

        foreach ($fileContent as $filename => $hash) {
            $this->write($filename, $hash);
        }

        $this->isInitialized = true;
    }

    /**
     * @return string
     */
    private function composeContentCacheFile(array $cacheEntries)
    {
        $data = '';

        foreach ($cacheEntries as $filename => $hash) {
            $data .= sprintf("'%s'=>'%s',", $filename, $hash);
        }

        return "<?php\nreturn [$data];";
    }
}
