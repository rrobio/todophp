<?php

namespace controllers;

interface Controller
{
    public function handle(string $uri): string;
}