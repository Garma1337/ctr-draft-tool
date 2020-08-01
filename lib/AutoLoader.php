<?php

namespace DraftTool\Lib;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;

/**
 * PSR-4 Autoloader
 * @author Garma
 */
class AutoLoader
{
    /**
     * @var string
     */
    private string $basePath;
    
    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }
    
    /**
     * Loads a class
     * @param string $name
     */
    public function loadClass(string $name): void
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        
        $splittedNamespace = explode('\\', $name);
        $standaloneClassName = array_pop($splittedNamespace);
        
        array_shift($splittedNamespace);
        $path = $this->basePath . '/' . strtolower($inflector->tableize(implode('/', $splittedNamespace))) . '/' . $standaloneClassName . '.php';
        
        if (file_exists($path)) {
            require_once $path;
        }
    }
    
    /**
     * Registers the autoloader
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }
}
