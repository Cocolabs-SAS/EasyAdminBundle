<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EasyCorp\Bundle\EasyAdminBundle\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * It provides a file system based cache exposing methods with the same names
 * as in the PSR-6 Cache standard.
 */
class CacheManager
{
    private FilesystemAdapter $cache;

    public function __construct($cacheDir)
    {
        $this->cache = new FilesystemAdapter('', 0, $cacheDir);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getItem($key)
    {
        return $this->cache->getItem($key)->get();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasItem($key)
    {
        return $this->cache->hasItem($key);
    }

    /**
     * @param string $key
     * @param mixed  $item
     * @param int    $lifetime
     *
     * @return bool
     */
    public function save($key, $item, $lifetime = 0)
    {
        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($item);
        $cacheItem->expiresAfter($lifetime);

        return $this->cache->save($cacheItem);
    }
}
