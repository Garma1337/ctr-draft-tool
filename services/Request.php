<?php

declare(strict_types = 1);

namespace DraftTool\Services;

/**
 * Request Wrapper
 * @author Garma
 */
class Request
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    
    /**
     * @var array
     */
    private $request;
    
    public function __construct()
    {
        $this->request = array_merge($_GET, $_POST);
    }
    
    /**
     * Checks if a request is of a certain method
     * @param string $method
     * @return bool
     */
    public function is(string $method): bool
    {
        return ($_SERVER['REQUEST_METHOD'] === $method);
    }
    
    /**
     * Checks if a request is a GET request
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->is(self::METHOD_GET);
    }
    
    /**
     * Checks if a request is a POST request
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->is(self::METHOD_POST);
    }
    
    /**
     * Returns the value of a request parameter
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function getParam(string $key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }
    
    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->request;
    }
    
    /**
     * Checks if a certain request parameter is set
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->request[$key]);
    }
}
