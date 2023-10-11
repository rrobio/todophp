<?php

namespace test;
include "../model/Todo.php";
use model\Todo;
use PHPUnit\Framework\TestCase;

class TodoTest extends TestCase
{

    public function testToggleTodo()
    {
        $todo = new Todo("example", false, false, 1);
        $this->assertSame($todo->done, false);
        $todo->toggleTodo();
        $this->assertSame($todo->done, true);
    }

    public function testToggleSkip()
    {
        $todo = new Todo("example", false, false, 1);
        $this->assertSame($todo->skip, false);
        $todo->toggleSkip();
        $this->assertSame($todo->skip, true);

    }

    public function test__construct()
    {
        $todo = new Todo("example", false, false, 1);
        $this->assertNotNull($todo);
        $this->assertSame($todo->text, "example");
        $this->assertSame($todo->done, false);
        $this->assertSame($todo->skip, false);
    }
    public function test__constructWithNullID() {
        $todo = new Todo("example", true, true);
        $this->assertNotNull($todo);
        $this->assertNotNull($todo->id);
    }
}
