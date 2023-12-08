<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TastTest extends TestCase
{
    public function testSomething(): void
    {
        $task = new Task();
        $task->setTitle('titre de test');
        $this->assertEmpty($task->getDeadLine());
        $this->assertEmpty($task->getUpdatedAt());
        $this->assertEquals('titre de test', $task->getTitle());
        $this->assertTrue(true);
    }
}
