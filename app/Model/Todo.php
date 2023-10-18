<?php
declare(strict_types=1);

namespace App\Model;
class Todo
{
    public function __construct(public string $text, public bool $done, public bool $skip, public ?int $id = null)
    {
    }

    public function toggleTodo(): void
    {
        $this->done = !$this->done;
    }

    public function toggleSkip(): void
    {
        $this->skip = !$this->skip;
    }
}