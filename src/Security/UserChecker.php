<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        /** @var User $owner */
        $owner = $user;
        if (!$owner->isActivated()) {
            $message = $this->translator->trans('app.twig.page.security.login.not_already_activated');
            throw new CustomUserMessageAccountStatusException($message, [], 100);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
