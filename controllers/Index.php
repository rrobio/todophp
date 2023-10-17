<?php

namespace controllers;

use model\Todo;
use SQLite3;
use template\TemplateEngine;

class Index implements Controller
{

    public function handle(string $uri): string
    {

        $db = new SQLite3('todo.db');

        $stmt = $db->prepare('SELECT * FROM todo');
        $items = $stmt->execute();
        $todoViews = array();
        while ($row = $items->fetchArray()) {
            $todo = new Todo($row['text'], (bool)$row['done'], (bool)$row['skip'], $row['id']);
            $todoViews[] = TemplateEngine::render_view('todo', [
                'id' => $todo->id,
                'text' => $todo->text,
                'doneChecked' => $todo->done ? 'checked' : '',
                'skipChecked' => $todo->skip ? 'checked' : '',
            ]);
        }
        print(TemplateEngine::render_view('index', [
            'app' => implode($todoViews),
        ]));
        return '';
    }
}