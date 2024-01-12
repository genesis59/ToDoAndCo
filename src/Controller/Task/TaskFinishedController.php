<?php

namespace App\Controller\Task;

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
        $paginationError = $paginatorService->create($taskRepository, $request, 'task_list_finished');
        if ($paginationError) {
            $this->addFlash('error', $paginationError['message']);

            return $this->redirectToRoute('homepage');
        }
        $parameters = [
            'tasks' => $paginatorService->getData(),
            'search' => $paginatorService->getSearch(),
            'currentPage' => $paginatorService->getCurrentPage(),
            'currentLimit' => $paginatorService->getLimit(),
            'firstPage' => $paginatorService->getUrlFirstPage(),
            'lastPage' => $paginatorService->getUrlLastPage(),
            'nextPage' => $paginatorService->getUrlNextPage(),
            'previousPage' => $paginatorService->getUrlPreviousPage(),
        ];
        if ($request->query->get('preview')) {
            return $this->render('components/task/_tasks.html.twig', $parameters);
        }

        return $this->render('task/tasks_finished.html.twig', $parameters);
    }
}
