<?php

namespace App\Subscriber;

use App\Event\UserEmailEvent;
use App\Mailer\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerService $mailerService,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEmailEvent::ACTIVATION_EMAIL => 'onUserCreate',
        ];
    }

    public function onUserCreate(UserEmailEvent $event): void
    {
        $this->mailerService->sendEmail(
            $this->translator->trans('app.email.activation.subject'),
            [
                'user' => $event->getUser(),
                'url' => $this->urlGenerator->generate(
                    'user_activation',
                    [
                        'token' => $event->getUser()->getActivationToken()->getToken(),
                        'uuidUser' => $event->getUser()->getUuid(),
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ],
            'activation'
        );
    }
}
