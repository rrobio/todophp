<?php

use controllers\Index;
use internal\Router;

Router::get('/', Index::class);
//Router::get('/todos', 'hello todos');

Router::get('/toggleSkipTodo?{id}', function (?string $id) {
    $db = new SQLite3('todo.db');
    \controllers\Todo::toggleStatus((int)$id, 'skip', $db);
    header('Location: /');
});

Router::get('/toggleDoneTodo?{id}', function (?string $id) {
    $db = new SQLite3('todo.db');
    \controllers\Todo::toggleStatus((int)$id, 'done', $db);
    header('Location: /');
});

Router::get('/createTodo?{text}', function (?string $text) {
    $db = new SQLite3('todo.db');
    $ret = \controllers\Todo::insertTodo($text ?? 'empty', $db);
    header('Location: /');
});

Router::get('/deleteTodo?{id}', function (?string $id) {
    $db = new SQLite3('todo.db');
    $ret = \controllers\Todo::removeTodo($id, $db);
    header('Location: /');
});


