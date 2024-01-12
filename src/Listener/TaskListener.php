<?php

namespace App\Listener;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

readonly class TaskListener
{
    public function __construct(
        private Security $security
    ) {
    }

    public function prePersist(Task $task, LifecycleEventArgs $args): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $task->setOwner($user);
        $task->setUuid(Uuid::v4());
    }
}
