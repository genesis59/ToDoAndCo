<?php

namespace App\Controller\User;

use App\Form\SearchType;
use App\Paginator\PaginatorService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    #[Route(path: '/users', name: 'user_list')]
    public function __invoke(
        Request $request,
        TranslatorInterface $translator,
        PaginatorService $paginatorService,
        UserRepository $userRepository
    ): Response {
        if (!$this->isGranted('USER_VIEW', $this->getUser())) {
            $this->addFlash('error', $translator->trans('app.flashes.user.denied_access'));

            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $request->request->set('q', $form->get('q')->getData());
        }

        $paginationError = $paginatorService->create(
            $userRepository,
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
            'form' => $form->createView(),
            'users' => $paginatorService->getData(),
            'currentPage' => $paginatorService->getCurrentPage(),
            'firstPage' => $paginatorService->getUrlFirstPage(),
            'lastPage' => $paginatorService->getUrlLastPage(),
            'nextPage' => $paginatorService->getUrlNextPage(),
            'previousPage' => $paginatorService->getUrlPreviousPage(),
        ]);
    }
}
