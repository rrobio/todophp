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
        if (preg_match('/(.*)(\{.*})/', $uri, $matches)) {
            $uri = implode("", array_map(function ($a) {
                return preg_replace('/({.*})/', '(.*)', $a);
            }, array_slice($matches, 0, 1)));

        }
        array_splice($matches, 0, 1);
        if (is_callable($controller)) {
            Router::$routes += [$uri => [$controller, $matches]];
        } elseif (class_exists($controller)) {
            Router::$routes += [$uri => [new $controller, $matches]];
        } else {
            Router::$routes += [$uri => [$controller, $matches]];
        }
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

        foreach (Router::$routes as $route => $controller) {
            if (!preg_match('/\*/', $route)) { // has no parts
                var_dump($route);
                if ($route === $uri) {
                    $this->handleController($route, $controller[0]);
                    return;
                }
            } else {
                var_dump($route);
                $args = $this->mapArgs($uri, $controller[1]);
                $this->handleController($route, $controller[0](...$args));
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
        if (is_callable($controller)) {
            print($controller());
            return;
        }
        match (gettype($controller)) {
            'NULL' => print(''),
            'string' => print($controller),
            'object' => print($controller->handle($route)),
        };
    }

    private function mapArgs(string $uri, array $parts): array
    {
        $route_parts = explode('/', $uri);
        $args = [];
        for ($i = 0; $i < count($parts); $i++) {
           if (str_contains($parts[$i], '{')) {
               $part = preg_replace('/[{}]/', '', $parts[$i]);
               $args += [ $part => $route_parts[$i]];
           }
        }
        return $args;
    }

    private function handleError(int $err): void
    {
        print((new Error($err))->handle(''));
    }

}

