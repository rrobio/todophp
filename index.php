<?php
declare(strict_types=1);

spl_autoload_register(function ($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    require $file;
});

include("model/todoElement.php");

use model\Todo;

$db = new SQLite3('todo.db');

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

if (isset($_POST['createTodo'])) {
    if (!isset($_POST['todoText'])) return false;
    insertTodo(new Todo($_POST['todoText'], false, false), $db);
    header('Location: index.php');
}

if (isset($_GET['deleteID'])) {
    $ret = removeTodo((int)$_GET['deleteID'], $db);
    header('Location: index.php');
}

if (isset($_GET['toggleSkip'])) {
    toggleStatus((int)$_GET['toggleSkip'], 'skip', $db);
    header('Location: index.php');
}

if (isset($_GET['toggleDone'])) {
    toggleStatus((int)$_GET['toggleDone'], 'done', $db);
    header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Todos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Todos</h1>
<div id="create-todo">
    <form method="post" action="index.php">
        <label for="new-todo-text">Todo Text: </label>
        <input type="text" id="new-todo-text"
               placeholder="create new todo" name="todoText" required>
        <input type="hidden" name="createTodo" value="1">
        <input class="button" id="new-todo" type="submit" value="Submit">
    </form>
</div>
<div id="app">
    <?php
    $stmt = $db->prepare('SELECT * FROM todo');
    $items = $stmt->execute();

    while ($row = $items->fetchArray()) {
        $todo = new Todo($row['text'], (bool)$row['done'], (bool)$row['skip'], $row['id']);
        print(model\generateNode($todo)->saveHTML());
    }
    ?>
</div>
</body>
</html>