<?php

declare(strict_types=1);

namespace App\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;

readonly class MailerService
{
    public function __construct(
        private TransportInterface $mailer,
        private UriSigner $uriSigner
    ) {
    }

    /**
     * @param array<string,mixed> $context
     *
     * @throws TransportExceptionInterface
     */
    public function sendEmail(string $subject, array $context, string $template): void
    {
        $signUrl = $this->uriSigner->sign($context['url']);
        $email = (new TemplatedEmail())
            ->from($context['user']->getEmail())
            ->to('me@example.com')
            ->subject($subject)
            ->htmlTemplate('emails/'.$template.'.html.twig')
            ->context([
                ...$context,
                'sign_url' => $signUrl,
            ])
        ;
        $this->mailer->send($email);
    }
}
