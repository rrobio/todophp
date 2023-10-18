<?php

namespace App\Controllers;

use SQLite3;

// TODO: change the static functions into private and use __invoke to call them
//       but first we need to update the router to handle member functions
class Todo implements Controller
{
    public static function insertTodo(string $text, SQLite3 $db): bool
    {
        $todo = new \App\Model\Todo($text, false, false);
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

    public static function removeTodo(int $id, SQLite3 $db): bool
    {
        $stmt = $db->prepare('DELETE FROM todo WHERE id=:id;');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        $result = $stmt->execute();
        return is_object($result);
    }

    public static function toggleStatus(int $id, string $status, SQLite3 $db): bool
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
    public function __invoke(string $uri): string
    {
        return '';
    }
}