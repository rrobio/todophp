<?php

use controllers\Index;
use internal\Router;
use model\Todo;


Router::get('/', Index::class);
Router::get('/todos', 'hello todos');
Router::get('/createTodo/{text}', function (?string $text) {
    $db = new SQLite3('todo.db');
    $ret = insertTodo(new Todo($text ?? 'hello', false, false), $db);
    header('Location: /');
});
Router::get('/deleteTodo/{id}', function (?string $id)  {
    if (is_null($id)) {
        return 'invalid id';
    }
    $db = new SQLite3('todo.db');
    $ret = removeTodo($id, $db);
    header('Location: /');
});


