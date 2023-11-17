<?php

namespace App\Controller\Task;

use App\Form\SearchType;
use App\Paginator\PaginatorService;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskFinishedController extends AbstractController
{
    #[Route(path: '/tasks/finished', name: 'task_list_finished')]
    public function __invoke(Request $request, TaskRepository $taskRepository, PaginatorService $paginatorService): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $request->request->set('q', $form->get('q')->getData());
        }
        $paginationError = $paginatorService->create(
            $taskRepository,
            $request,
            'task_list_finished'
        );
        if ($paginationError) {
            $this->addFlash('error', $paginationError['message']);
            $response = $this->render('task/list_finished.html.twig', [
                'tasks' => null,
            ]);
            $response->setStatusCode(intval($paginationError['code']));

            return $response;
        }

        return $this->render(
            'task/list_finished.html.twig',
            [
                'form' => $form->createView(),
                'tasks' => $paginatorService->getData(),
                'currentPage' => $paginatorService->getCurrentPage(),
                'firstPage' => $paginatorService->getUrlFirstPage(),
                'lastPage' => $paginatorService->getUrlLastPage(),
                'nextPage' => $paginatorService->getUrlNextPage(),
                'previousPage' => $paginatorService->getUrlPreviousPage(),
            ]
        );
    }
}
