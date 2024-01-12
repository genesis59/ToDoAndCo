<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class TaskTest extends TestCase
{
    private Task $task;
    private User $user;

    protected function setUp(): void
    {
        // Initialisation commune avant chaque test
        $this->task = new Task();
        $this->user = new User();
    }

    public function testCreatedAt(): void
    {
        $this->assertNotEmpty($this->task->getCreatedAt());
    }

    public function testDeadLine(): void
    {
        $now = new DateTime();
        $this->task->setDeadLine($now);
        $this->assertEquals($now, $this->task->getDeadLine());
    }

    public function testUpdatedAt(): void
    {
        $nowImmutable = new DateTimeImmutable();
        $this->task->setUpdatedAt($nowImmutable);
        $this->assertEquals($nowImmutable, $this->task->getUpdatedAt());
    }

    public function testTitle(): void
    {
        $this->task->setTitle('titre de test');
        $this->assertEquals('titre de test', $this->task->getTitle());
    }

    public function testContent(): void
    {
        $this->task->setContent('contenu de test');
        $this->assertEquals('contenu de test', $this->task->getContent());
    }

    public function testUuid(): void
    {
        $uuid = Uuid::v4();
        $this->task->setUuid($uuid);
        $this->assertEquals($uuid, $this->task->getUuid());
    }

    public function testOwner(): void
    {
        $this->task->setOwner($this->user);
        $this->assertEquals($this->user, $this->task->getOwner());
    }

    public function testIsDone(): void
    {
        $this->assertIsBool($this->task->isDone());
        $this->assertFalse($this->task->isDone());

        $this->task->toggle(true);
        $this->assertTrue($this->task->isDone());

        $this->task->setIsDone(false);
        $this->assertFalse($this->task->isDone());
    }

    public function testId(): void
    {
        $this->assertEmpty($this->task->getId());
    }
}
