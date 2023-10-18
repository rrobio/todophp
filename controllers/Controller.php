<?php

namespace controllers;

interface Controller
{
    public function __invoke(string $uri): string;
}