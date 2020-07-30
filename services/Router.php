<?php

declare(strict_types = 1);

namespace DraftTool\Services;

/**
 * URL Handling functions
 * @author Garma
 */
class Router
{
    /**
     * Generates a URL
     * @param string $action
     * @param array $params
     * @return string
     */
    public function generateUrl(string $action, array $params = []): string
    {
        $url = $this->getBaseUrl() . 'index.php?action=' . $action;
        
        if (count($params) > 0) {
            $url .= '&' . http_build_query($params);
        }
        
        return $url;
    }
    
    /**
     * Returns the base url without any parameters
     * @return string
     */
    public function getBaseUrl(): string
    {
        $scriptFilename = basename($_SERVER['PHP_SELF']);
        $basePath = str_replace($scriptFilename, '', $_SERVER['PHP_SELF']);
        
        $scheme = $_SERVER['REQUEST_SCHEME'];
        $host = $_SERVER['HTTP_HOST'];
        
        $url = $scheme . '://' . $host;
        
        if (!empty($basePath)) {
            $url .= $basePath;
        } else {
            $url .= '/';
        }
        
        return $url;
    }
}
