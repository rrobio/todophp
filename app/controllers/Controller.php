<?php

namespace App\Controllers;

interface Controller
{
    public function __invoke(string $uri): string;
}