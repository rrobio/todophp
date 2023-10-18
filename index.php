<?php
declare(strict_types=1);
require 'vendor/autoload.php';

use App\Internal\Router;
use JetBrains\PhpStorm\NoReturn;
const BASE_DIR = __DIR__;
$router = new Router('/app/Routes/web.php');
$router->serve();

#[NoReturn] function dd($array, $name = 'var'): never
{
    highlight_string("<?php\n\$$name =\n" . var_export($array, true) . ";\n?>");
    die();
}