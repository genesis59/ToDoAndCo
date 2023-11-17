<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Event\UserEmailEvent;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserCreateController extends AbstractController
{
    #[Route(path: '/users/create', name: 'user_create')]
    public function __invoke(Request $request, ManagerRegistry $managerRegistry, TranslatorInterface $translator, EventDispatcherInterface $dispatcher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $user->setRoles([$form->get('roles')->getData()]);
            $em->persist($user);
            $em->flush();
            $dispatcher->dispatch(new UserEmailEvent($user), UserEmailEvent::ACTIVATION_EMAIL);
            $this->addFlash('success', $translator->trans('app.flashes.user.created'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }
}
