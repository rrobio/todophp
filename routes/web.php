<?php

use controllers\Index;
use internal\Router;
use model\Todo;


Router::get('/', Index::class);
//Router::get('/todos', 'hello todos');

Router::get('/toggleSkipTodo?{id}', function (?string $id) {
    $db = new SQLite3('todo.db');
    toggleStatus((int)$id, 'skip', $db);
    header('Location: /');
});

Router::get('/toggleDoneTodo?{id}', function (?string $id) {
    $db = new SQLite3('todo.db');
    toggleStatus((int)$id, 'done', $db);
    header('Location: /');
});

Router::get('/createTodo?{text}', function (?string $text) {
    $db = new SQLite3('todo.db');
    $ret = insertTodo(new Todo($text, false, false), $db);
    header('Location: /');
});

Router::get('/deleteTodo?{id}', function (?string $id)  {
    $db = new SQLite3('todo.db');
    $ret = removeTodo($id, $db);
    header('Location: /');
});


