<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\Token;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        // Initialisation commune avant chaque test
        $this->user = new User();
        $this->user->setUuid(Uuid::v4());
        $this->user->setActivationToken(new Token());
        $this->user->setCreatedAt(new DateTime());
        $this->user->addTask(new Task());
        $this->user->addTask(new Task());
    }

    public function testUsername(): void
    {
        $this->user->setUsername('usernameTest');
        $this->assertEquals('usernameTest', $this->user->getUsername());
    }

    public function testCreatedAt(): void
    {
        $now = new DateTime();
        $this->user->setCreatedAt($now);
        $this->assertEquals($now, $this->user->getCreatedAt());
    }

    public function testUuid(): void
    {
        $uuid = Uuid::v4();
        $this->user->setUuid($uuid);
        $this->assertEquals($uuid, $this->user->getUuid());
    }

    public function testEmail(): void
    {
        $this->user->setEmail('email@test.com');
        $this->assertEquals('email@test.com', $this->user->getEmail());
        $this->assertEquals('email@test.com', $this->user->getUserIdentifier());
    }

    public function testPassword(): void
    {
        $this->user->setPassword('PASSWORD');
        $this->assertEquals('PASSWORD', $this->user->getPassword());
    }

    public function testActivationToken(): void
    {
        $token = new Token();
        $this->user->setActivationToken($token);
        $this->assertEquals($token, $this->user->getActivationToken());
    }

    public function testIsActivated(): void
    {
        $this->assertFalse($this->user->isActivated());
        $this->user->setActivated(true);
        $this->assertTrue($this->user->isActivated());
    }

    public function testRoles(): void
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testTasks(): void
    {
        $this->assertCount(2, $this->user->getTasks());
        $this->user->removeTask($this->user->getTasks()[0]);
        $this->assertCount(1, $this->user->getTasks());
    }

    public function testEraseCredentials(): void
    {
        $this->user->setPassword('PASSWORD');
        $this->user->eraseCredentials();
        $this->assertNotEmpty($this->user->getPassword());
    }

    public function testId(): void
    {
        $this->assertEmpty($this->user->getId());
    }
}
