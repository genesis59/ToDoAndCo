<?php

namespace App\Listener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class UserListener
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
        if (!count($user->getRoles())) {
            $user->setRoles([User::ROLE_USER]);
        }
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new \DateTime());
        $user->setUuid(Uuid::v4());
    }
}
