<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\UserEditType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserEditController extends AbstractController
{
    #[Route(path: '/users/{uuid}/edit', name: 'user_edit')]
    public function __invoke(User $user, Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        if (!$this->isGranted('USER_EDIT', $this->getUser())) {
            $this->addFlash('error', $translator->trans('app.flashes.user.denied_access'));

            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(UserEditType::class, null, [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setRoles([$form->get('roles')->getData()]);
            $managerRegistry->getManager()->flush();
            $this->addFlash('success', $translator->trans('app.flashes.user.updated'));

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
