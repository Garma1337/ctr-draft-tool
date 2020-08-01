<?php

declare(strict_types = 1);

namespace DraftTool\Lib;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use DraftTool\Commands\MigrationRunCommand;
use DraftTool\Services\Cache;
use DraftTool\Services\Draft;
use DraftTool\Services\MigrationManager;
use DraftTool\Services\Request;
use DraftTool\Services\Router;
use DraftTool\Services\Translator;
use ReflectionClass;
use ReflectionException;
use Smarty;
use Symfony\Component\Console\Application;

/**
 * Central application class that mostly acts as a service container
 * @author Garma
 */
class App
{
    /**
     * @var array
     */
    private static array $services;
    
    /**
     * @return Application
     */
    public static function application(): Application
    {
        if (isset(self::$services['application'])) {
            return self::$services['application'];
        }
        
        $application = new Application('DraftTool', '1.0.0');
        $application->add(new MigrationRunCommand());
        
        self::$services['application'] = $application;
        
        return $application;
    }
    
    /**
     * @return Cache
     */
    public static function cache(): Cache
    {
        if (isset(self::$services['cache'])) {
            return self::$services['cache'];
        }
        
        $cache = new Cache();
        self::$services['cache'] = $cache;
        
        return $cache;
    }
    
    /**
     * Returns a config value
     * @param string $path
     * @return mixed
     */
    public static function config(string $path)
    {
        $config = include __DIR__ . '/../config.php';
        return Arr::path($config, $path);
    }
    
    /**
     * @return Connection
     * @throws DBALException
     */
    public static function db(): Connection
    {
        if (isset(self::$services['db'])) {
            return self::$services['db'];
        }
        
        $db = DriverManager::getConnection(self::config('db'));
        
        self::$services['db'] = $db;
        
        return $db;
    }
    
    /**
     * @return Draft
     */
    public static function draft(): Draft
    {
        if (isset(self::$services['draft'])) {
            return self::$services['draft'];
        }
        
        $draft = new Draft();
        self::$services['draft'] = $draft;
        
        return $draft;
    }
    
    /**
     * @return MigrationManager
     */
    public static function migrationManager(): MigrationManager
    {
        if (isset(self::$services['migration_manager'])) {
            return self::$services['migration_manager'];
        }
        
        $migrationManager = new MigrationManager(__DIR__ . '/../migrations/');
        self::$services['migration_manager'] = $migrationManager;
        
        return $migrationManager;
    }
    
    /**
     * @return Request
     */
    public static function request(): Request
    {
        if (isset(self::$services['request'])) {
            return self::$services['request'];
        }
        
        $request = new Request();
        self::$services['request'] = $request;
        
        return $request;
    }
    
    /**
     * @return Router
     */
    public static function router(): Router
    {
        if (isset(self::$services['router'])) {
            return self::$services['router'];
        }
        
        $router = new Router();
        self::$services['router'] = $router;
        
        return $router;
    }
    
    /**
     * @return Smarty
     */
    public static function template(): Smarty
    {
        if (isset(self::$services['smarty'])) {
            return self::$services['smarty'];
        }
        
        $smarty = (new Smarty())
            ->setTemplateDir(__DIR__ . '/../templates/')
            ->setCompileDir(__DIR__ . '/../templates/compiled/')
        ;
        
        $smarty->setCaching(0);
        $smarty->setEscapeHtml(true);
        
        self::$services['smarty'] = $smarty;
        
        return $smarty;
    }
    
    /**
     * @return Translator
     */
    public static function translator(): Translator
    {
        if (isset(self::$services['translator'])) {
            return self::$services['translator'];
        }
        
        $translator = new Translator(__DIR__ . '/../lib/translation/');
        self::$services['translator'] = $translator;
        
        return $translator;
    }
    
    /**
     * Dispatches the request
     * @throws ReflectionException
     */
    public static function dispatchRequest(): void
    {
        $action = self::request()->getParam('action', 'index');
        
        $controller = new Controller();
        $actionMethod = $action . 'Action';
        
        $reflectionClass = new ReflectionClass($controller);
        
        if (!$reflectionClass->hasMethod($actionMethod)) {
            echo 'Action "' . $action . '" not found.';
            die();
        }
        
        session_start();
        
        $controller->$actionMethod();
    }
}
