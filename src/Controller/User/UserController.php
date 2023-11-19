<?php

namespace App\Controller\User;

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
        $paginationError = $paginatorService->create($userRepository, $request, 'user_list');
        if ($paginationError) {
            $this->addFlash('error', $paginationError['message']);
            $response = $this->render('task/list_todo.html.twig', ['tasks' => null]);
            $response->setStatusCode(intval($paginationError['code']));

            return $response;
        }
        $parameters = [
            'users' => $paginatorService->getData(),
            'search' => $paginatorService->getSearch(),
            'currentPage' => $paginatorService->getCurrentPage(),
            'currentLimit' => $paginatorService->getLimit(),
            'firstPage' => $paginatorService->getUrlFirstPage(),
            'lastPage' => $paginatorService->getUrlLastPage(),
            'nextPage' => $paginatorService->getUrlNextPage(),
            'previousPage' => $paginatorService->getUrlPreviousPage(),
        ];
        if ($request->query->get('preview')) {
            return $this->render('components/user/_users.html.twig', $parameters);
        }

        return $this->render('user/users.html.twig', $parameters);
    }
}
