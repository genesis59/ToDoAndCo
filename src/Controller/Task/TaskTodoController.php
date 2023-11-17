<?php

namespace App\Controller\Task;

use App\Form\SearchType;
use App\Paginator\PaginatorService;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskTodoController extends AbstractController
{
    #[Route(path: '/tasks/todo', name: 'task_list_todo')]
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
            'task_list_todo'
        );
        if ($paginationError) {
            $this->addFlash('error', $paginationError['message']);
            $response = $this->render('task/list_todo.html.twig', [
                'tasks' => null,
            ]);
            $response->setStatusCode(intval($paginationError['code']));

            return $response;
        }

        return $this->render('task/list_todo.html.twig', [
            'form' => $form->createView(),
            'tasks' => $paginatorService->getData(),
            'currentPage' => $paginatorService->getCurrentPage(),
            'firstPage' => $paginatorService->getUrlFirstPage(),
            'lastPage' => $paginatorService->getUrlLastPage(),
            'nextPage' => $paginatorService->getUrlNextPage(),
            'previousPage' => $paginatorService->getUrlPreviousPage(),
        ]);
    }
}
