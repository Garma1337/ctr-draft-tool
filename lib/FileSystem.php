<?php

declare(strict_types = 1);

namespace DraftTool\Lib;

/**
 * File system functions
 * @author
 */
class FileSystem
{
    /**
     * Gets all files inside a directory and ignores the hidden files . and .. on Linux systems
     * @param string $directory
     * @return array
     */
    public static function getFilesInDirectory(string $directory): array
    {
        $files = [];
        
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $file;
            }
        }
        
        return $files;
    }
}
