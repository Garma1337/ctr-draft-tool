<?php

declare(strict_types = 1);

namespace DraftTool\Lib;

/**
 * Array functions
 * @author Garma
 */
class Arr
{
    /**
     * Returns a path value from an array
     * @param array $array
     * @param string $path
     * @return mixed
     */
    public static function path(array $array, string $path)
    {
        $explodedPath = explode('.', $path);
        
        foreach ($explodedPath as $key) {
            $array = &$array[$key];
        }
        
        $value = $array;
        unset($array);
        
        return $value;
    }
}
