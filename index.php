<?php
declare(strict_types=1);

use internal\Router;
use JetBrains\PhpStorm\NoReturn;

spl_autoload_register(function ($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    require $file;
});
const BASE_DIR = __DIR__;
$router = new Router();
$router->serve();

#[NoReturn] function dd($array, $name = 'var'): never
{
    highlight_string("<?php\n\$$name =\n" . var_export($array, true) . ";\n?>");
    die();
}