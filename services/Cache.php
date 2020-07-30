<?php

declare(strict_types = 1);

namespace DraftTool\Services;

use InvalidArgumentException;

/**
 * Simple, static PHP Cache
 * @author Garma
 */
class Cache
{
    /**
     * @var array
     */
    protected array $cache;
    
    /**
     * Stores a value in the cache
     * @param string $key
     * @param $value
     * @param bool $override
     */
    public function save(string $key, $value, bool $override = false): void
    {
        if (!$this->has($key) || $override) {
            $this->cache[$key] = $value;
        } else {
            throw new InvalidArgumentException('Cache variable "' . $key . '" already exists.');
        }
    }
    
    /**
     * Loads a variable from the cache
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function load(string $key, $default = null)
    {
        return $this->cache[$key] ?? $default;
    }
    
    /**
     * Checks if a cache variable is already set
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return (isset($this->cache[$key]) && $this->cache[$key] !== null);
    }
    
    /**
     * Deletes a variable from the cache
     * @param string $key
     */
    public function delete(string $key): void
    {
        $this->cache[$key] = null;
        unset($this->cache[$key]);
    }
}
