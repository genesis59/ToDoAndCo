<?php

namespace App\Controller\User;

use App\Entity\Token;
use App\Entity\User;
use App\Event\UserEmailEvent;
use App\Form\NewActivationType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserNewActivationController extends AbstractController
{
    #[Route('/users/new-activation', name: 'user_new_activation')]
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        TokenRepository $tokenRepository,
        TokenGeneratorInterface $tokenGenerator,
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(NewActivationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $userRepository->findOneBy(['email' => $form->getData()['email']]);

            if ($user == null) {
                $this->addFlash('error', $translator->trans('app.flashes.new_activation.error'));

                return $this->redirectToRoute('user_new_activation');
            }
            if ($user->isActivated()) {
                $this->addFlash('error', $translator->trans('app.flashes.new_activation.already_activation'));

                return $this->redirectToRoute('app_login');
            }

            $token = new Token();
            $tokenRepository->save($token, true);
            $userRepository->createActivationToken($user, $token);
            $dispatcher->dispatch(new UserEmailEvent($user), UserEmailEvent::ACTIVATION_EMAIL);
            $this->addFlash('success', $translator->trans('app.flashes.new_activation.success'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/new_activation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
