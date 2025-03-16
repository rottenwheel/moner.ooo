<?php

declare(strict_types=1);
class FileCache
{
    private $cacheDir;
    private $defaultExpiration;

    public function __construct($cacheDir = '../storage/cache', $defaultExpiration = 60)
    {
        $this->cacheDir = $cacheDir;
        $this->defaultExpiration = $defaultExpiration;
        // Create the cache directory if it doesn't exist
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get($key, $expiration = null)
    {
        $file = $this->cacheDir . '/' . md5($key) . '.cache';
        if (file_exists($file)) {
            $expiration = $expiration ?? $this->defaultExpiration;
            if (time() - filemtime($file) < $expiration) {
                $data = file_get_contents($file);
                return unserialize($data);
            } else {
                unlink($file);
            }
        }
        return null;
    }

    public function set($key, $data): void
    {
        $file = $this->cacheDir . '/' . md5($key) . '.cache';
        file_put_contents($file, serialize($data));
    }

    public function delete($key): void
    {
        $file = $this->cacheDir . '/' . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function clear()
    {
        array_map('unlink', glob($this->cacheDir . '/*.cache'));
    }

    public function getOrSet($key, callable $callback, ?int $expiration = null)
    {
        $data = $this->get($key, $expiration);
        if ($data === null) {
            $data = $callback(); // Execute the callback to get the data
            $this->set($key, $data);
        }
        return $data;
    }
}
