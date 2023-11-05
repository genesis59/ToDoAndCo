<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    private function canDelete(Task $subject, UserInterface $user): bool
    {
        if ($subject->getOwner()->getUsername() === 'Anonyme' && in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return true;
        }
        if ($subject->getOwner() === $user) {
            return true;
        }

        return false;
    }

    private function canEdit(Task $subject, UserInterface $user): bool
    {
        if ($subject->getOwner()->getUsername() === 'Anonyme' && in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return true;
        }
        if ($subject->getOwner() === $user) {
            return true;
        }

        return false;
    }
}
