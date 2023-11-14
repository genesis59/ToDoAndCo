<?php

namespace App\Listener;

use App\Entity\Token;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

readonly class TokenListener
{
    public function __construct(
        private TokenGeneratorInterface $tokenGenerator
    ) {
    }

    public function prePersist(Token $token, LifecycleEventArgs $args): void
    {
        $token->setToken($this->tokenGenerator->generateToken());
        $date = new \DateTimeImmutable();
        $token->setCreatedAt($date);
        $token->setExpiredAt($date->modify('+1 day'));
    }
}
