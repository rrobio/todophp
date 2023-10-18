<?php

namespace App\Controllers;

use App\Template\TemplateEngine;

readonly class Error implements Controller
{

    public function __construct(private int $error)
    {
    }

    public function __invoke(string $uri): string
    {
        return match ($this->error) {
            404 => TemplateEngine::render_view('404', []),
        };
    }
}