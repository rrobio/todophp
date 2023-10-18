<?php

namespace controllers;

use template\TemplateEngine;

class Error implements Controller
{

    public function __construct(private readonly int $error)
    {
    }

    public function __invoke(string $uri): string
    {
        return match ($this->error) {
            404 => TemplateEngine::render_view('404', []),
        };
    }
}