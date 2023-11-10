<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Paginator\PaginatorService;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly PaginatorService $paginatorService
    ) {
    }

    #[Route(path: '/users', name: 'user_list')]
    public function list(Request $request): Response
    {
        if (!$this->isGranted('USER_VIEW', $this->getUser())) {
            $this->addFlash('error', $this->translator->trans('app.flashes.user.denied_access'));

            return $this->redirectToRoute('homepage');
        }

        $paginationError = $this->paginatorService->create(
            $this->userRepository,
            $request,
            'user_list'
        );
        if ($paginationError) {
            $this->addFlash('error', $paginationError['message']);
            $response = $this->render('task/list_todo.html.twig', [
                'tasks' => null,
            ]);
            $response->setStatusCode(intval($paginationError['code']));

            return $response;
        }

        return $this->render('user/list.html.twig', [
            'users' => $this->paginatorService->getData(),
            'currentPage' => $this->paginatorService->getCurrentPage(),
            'firstPage' => $this->paginatorService->getUrlFirstPage(),
            'lastPage' => $this->paginatorService->getUrlLastPage(),
            'nextPage' => $this->paginatorService->getUrlNextPage(),
            'previousPage' => $this->paginatorService->getUrlPreviousPage(),
        ]);
    }

    #[Route(path: '/users/create', name: 'user_create')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $user->setRoles([$form->get('roles')->getData()]);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', $this->translator->trans('app.flashes.user.created'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/users/{uuid}/edit', name: 'user_edit')]
    public function edit(User $user, Request $request): Response
    {
        if (!$this->isGranted('USER_EDIT', $this->getUser())) {
            $this->addFlash('error', $this->translator->trans('app.flashes.user.denied_access'));

            return $this->redirectToRoute('homepage');
        }
        $saveHashPassword = $user->getPassword();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([$form->get('roles')->getData()]);
            $user->setPassword($saveHashPassword);
            $this->managerRegistry->getManager()->flush();
            $this->addFlash('success', $this->translator->trans('app.flashes.user.updated'));

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
