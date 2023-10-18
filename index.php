<?php
declare(strict_types=1);

use internal\Router;
use model\Todo;

spl_autoload_register(function ($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    require $file;
});
const BASE_DIR = __DIR__;
$router = new Router();
$router->serve();


function dd($array, $name = 'var') {
    highlight_string("<?php\n\$$name =\n" . var_export($array, true) . ";\n?>");
    die();
}

//
function insertTodo(Todo $todo, SQLite3 $db): bool
{
    $binds = array(
        ':text' => [$todo->text, SQLITE3_TEXT],
        ':done' => [$todo->done, SQLITE3_INTEGER],
        ':skip' => [$todo->skip, SQLITE3_INTEGER]
    );

    $stmt = $db->prepare('INSERT INTO todo (id, text, done, skip) VALUES (null, :text, :done, :skip);');

    foreach ($binds as $key => $value) {
        $stmt->bindValue($key, $value[0], $value[1]);
    }

    $ret = $stmt->execute();
    return is_object($ret);
}
//
function removeTodo(int $id, SQLite3 $db): bool
{
    $stmt = $db->prepare('DELETE FROM todo WHERE id=:id;');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

    $result = $stmt->execute();
    return is_object($result);
}
function toggleStatus(int $id, string $status, SQLite3 $db): bool
{
    $stmt = $db->prepare('SELECT * FROM todo WHERE id=:id;');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    if (is_object($result)) {
        $row = $result->fetchArray();
        $state = (bool)$row[$status];
        $state = !$state;
        $binds = array(
            ':status' => [intval($state), SQLITE3_INTEGER],
            ':id' => [$id, SQLITE3_INTEGER]
        );
        $stmt = $db->prepare('UPDATE todo SET ' . $status . ' = :status WHERE id=:id;');

        foreach ($binds as $key => $value) {
            $stmt->bindValue($key, $value[0], $value[1]);
        }

        $ret = $stmt->execute();
        return is_object($ret);
    }
    return false;
}
//
//    if (is_object($result)) {
//        $row = $result->fetchArray();
//        $state = (bool)$row[$status];
//        $state = !$state;
//        $binds = array(
//            ':status' => [intval($state), SQLITE3_INTEGER],
//            ':id' => [$id, SQLITE3_INTEGER]
//        );
//        $stmt = $db->prepare('UPDATE todo SET ' . $status . ' = :status WHERE id=:id;');
//
//        foreach ($binds as $key => $value) {
//            $stmt->bindValue($key, $value[0], $value[1]);
//        }
//
//        $ret = $stmt->execute();
//        return is_object($ret);
//    }
//    return false;
//}
//
//if (isset($_POST['createTodo'])) {
//    if (!isset($_POST['todoText'])) return false;
//    insertTodo(new Todo($_POST['todoText'], false, false), $db);
//    header('Location: index.php');
//}
//
//if (isset($_GET['deleteID'])) {
//}
//
//if (isset($_GET['toggleSkip'])) {
//    toggleStatus((int)$_GET['toggleSkip'], 'skip', $db);
//    header('Location: index.php');
//}
//
//if (isset($_GET['toggleDone'])) {
//    toggleStatus((int)$_GET['toggleDone'], 'done', $db);
//    header('Location: index.php');
//}
//