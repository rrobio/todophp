<?php

namespace internal;

use controllers\Controller;
use controllers\Error;

class Router
{
    // route = [URI => [HANDLER, PARTS]
    //  => ['/deleteTodo/id' => [callback, [ 'id' ]]
    static protected array $routes = array();

    public function __construct()
    {
        include_once(BASE_DIR . '/routes/web.php');
    }

    public static function get(string $uri, string|Controller|callable $controller): void
    {
        $uri = Router::trimSlashes($uri);
        $matches = array();

        //get base uri in first capture group and all the parameters in the next capture group
        preg_match('/(.*)\?\{(.*)}/', $uri, $matches);

        if ($matches) { // contains parameters
            $uri = $matches[1]; // get uri without parameters
        }
        // drop matched text and base uri, otherwise it's just an empty array
        array_splice($matches, 0, 2);

        // if it's a class instantiate it
        if (is_string($controller) && class_exists($controller)) {
            $controller = new $controller;
        }
        Router::$routes += [$uri => [$controller, $matches]];
    }

    private static function trimSlashes(string $uri): string
    {
        if (str_ends_with($uri, '/'))
            $uri = substr_replace($uri, "", -1);
        if (str_starts_with($uri, '/'))
            $uri = substr_replace($uri, "", 0, 1);

        return $uri;
    }

    public function serve(): void
    {
        $uri = $this->getUri();
        $uriParts = explode('?', $uri);

        $uriBase = $uriParts[0]; // get base uri
        $uriArgs = array_slice($uriParts, 1); // get the rest of the uri ie the parameters

        foreach (Router::$routes as $route => $controller) {
            if (!str_contains($uri, '=')) { // has no parameters
                if ($route === $uriBase) {
                    $this->handleController($route, $controller[0]);
                    return;
                }
            } elseif ($route === $uriBase) {
                $args = $this->mapArgs($uriArgs[0], $controller[1]);
                $this->handleController($uri, $controller[0](...$args));
                return;
            }
        }

        $this->handleError(404);
    }

    private static function getUri(): string
    {
        return Router::trimSlashes($_SERVER['REQUEST_URI']);
    }

    private function handleController(string $route, $controller): void
    {
        match (gettype($controller)) {
            'string' => print($controller),
            'object' => print($controller($route)), // classes and callbacks
        };
    }

    private function mapArgs(string $uri, array $parts): array
    {
        $uriParts = explode('&', $uri);

        $args = array();
        for ($i = 0; $i < count($uriParts); $i++) {
            $value = explode('=', $uriParts[$i]);
            if (!in_array($value[0], $parts)) {
                throw new \Exception('invalid parameters');
            }
            $args += [$value[0] => $value[1]];
        }
        return $args;
    }

    private function handleError(int $err): void
    {
        $errorHandler = new Error($err);
        print($errorHandler(''));
    }

}

