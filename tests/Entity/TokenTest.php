<?php

namespace App\Tests\Entity;

use App\Entity\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    private Token $token;

    protected function setUp(): void
    {
        // Initialisation commune avant chaque test
        $this->token = new Token();
    }

    public function testToken(): void
    {
        $this->token->setToken('XXXXX');
        $this->assertEquals('XXXXX', $this->token->getToken());
    }

    public function testExpiredAt(): void
    {
        $now = new DateTimeImmutable();
        $this->token->setExpiredAt($now);
        $this->assertEquals($now, $this->token->getExpiredAt());
    }

    public function testCreatedAt(): void
    {
        $now = new DateTimeImmutable();
        $this->token->setCreatedAt($now);
        $this->assertEquals($now, $this->token->getCreatedAt());
    }

    public function testId(): void
    {
        $this->assertEmpty($this->token->getId());
    }
}
