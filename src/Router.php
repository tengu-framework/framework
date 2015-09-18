<?php
namespace Tengu;

use Exception;

class Router
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $path;

    /**
     * Set the controller directory path.
     *
     * @param string $path
     *
     * @return void
     */
    public function setPath($path)
    {
        if (is_dir($path) === false) {
            throw new Exception('Invalid Controller path: "'.$path.'"');
        }

        $this->path = $path;
    }

    /**
     * Load and instantiate the correct controller class.
     *
     * @return void
     */
    public function handle()
    {
        $this->getController();

        if (is_readable($this->file) === false) {
            throw new Exception('404: File not found ("'.$this->file.'")');
        }

        include $this->file;

        $class      = ucfirst($this->controller);
        $controller = new $class;

        $action = $this->action;

        $controller->$action();
    }

    /**
     * Find and validate the requested controller.
     *
     * @return void
     */
    public function getController()
    {
        $uri = trim(substr($_SERVER['REQUEST_URI'], 1));
        $uri = rtrim($uri, '/');

        $route = $this->parseRoutes($uri);

        if (! empty($route)) {
            $uri = $route;
        }

        $parts            = explode('/', $uri);
        $this->controller = $parts[0];

        if (isset($parts[1])) {
            $this->action = $parts[1];
        } else {
            $this->action = 'index';
        }

        $this->file = $this->path.'/'.ucfirst($this->controller).'.php';
    }

    /**
     * Parse routes as defined in the routes configuration file.
     *
     * @param string $uri
     *
     * @return void
     */
    private function parseRoutes($uri)
    {
        include(APP_PATH.'/http/routes.php');

        if (isset($routes[$uri])) {
            $redirect = $routes[$uri];
        } else {
            foreach ($routes as $key => $value) {
                $key = str_replace(
                    [':any', 'alnum', ':num', ':alpha', ':segment'],
                    ['.+', '[[:alnum:]]+', '[[:digit:]]+', '[[:alpha:]]+', '[^/]*'],
                    $key
                );

                if (preg_match('#^'.$key.'$#', $uri, $matches)) {
                    array_shift($matches);

                    array_unshift($matches, 'temporary');
                    unset($matches[0]);

                    $routedUri = $value;

                    foreach ($matches as $matchKey => $matchValue) {
                        $routedUri = str_replace('$'.$matchKey, $matchValue, $routedUri);
                    }

                    $redirect = $routedUri;
                }
            }
        }

        if (isset($redirect)) {
            return $redirect;
        }
    }
}
