<?php

namespace App\Controller\User;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserActivationController extends AbstractController
{
    #[Route('/users/activation/{token}/{uuidUser}', name: 'user_activation')]
    public function __invoke(
        string $token,
        string $uuidUser,
        UserRepository $userRepository,
        TokenRepository $tokenRepository,
        TranslatorInterface $translator,
        UriSigner $uriSigner,
        Request $request
    ): Response {
        if (!$uriSigner->checkRequest($request)) {
            $this->addFlash('danger', $translator->trans('app.flashes.activation.invalid_token'));

            return $this->redirectToRoute('homepage');
        }
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        /** @var User $user */
        $user = $userRepository->findOneBy(['uuid' => $uuidUser]);
        /** @var Token $token */
        $token = $tokenRepository->findOneBy(['token' => $token]);
        if ($user == null || $token == null) {
            $this->addFlash('danger', $translator->trans('app.flashes.activation.error'));

            return $this->redirectToRoute('homepage');
        }

        if ($user->isActivated()) {
            $this->addFlash('info', $translator->trans('app.flashes.activation.already_activation'));

            return $this->redirectToRoute('homepage');
        }

        if ($token->getExpiredAt() < (new \DateTimeImmutable())) {
            $this->addFlash('danger', $translator->trans('app.flashes.activation.activation_time'));

            return $this->redirectToRoute('user_new_activation');
        }
        $userRepository->activate($user);
        $this->addFlash('success', $translator->trans('app.flashes.activation.success'));

        return $this->redirectToRoute('homepage');
    }
}
