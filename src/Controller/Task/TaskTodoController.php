<?php

namespace App\Controller\Task;

use App\Paginator\PaginatorService;
use App\Repository\TaskRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TaskTodoController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    #[Route(path: '/tasks/todo', name: 'task_list_todo')]
    public function __invoke(
        Request $request,
        TaskRepository $taskRepository,
        PaginatorService $paginatorService,
        TagAwareCacheInterface $cache
    ): Response {
        $key = sprintf(
            'tasksTodo-%s-%s-%s',
            intval($request->get('page', 1)),
            intval($request->get('limit', $this->getParameter('default_task_per_page'))),
            $request->get('q', '')
        );
        $parameters = $cache->get(
            $key,
            function (ItemInterface $item) use ($paginatorService, $taskRepository, $request) {
                $item->tag('tasksTodoCache');
                $item->expiresAfter(random_int(0, 300) + 3300);
                $paginationError = $paginatorService->create($taskRepository, $request, 'task_list_todo');
                if ($paginationError) {
                    $this->addFlash('error', $paginationError['message']);

                    return $this->redirectToRoute('homepage');
                }

                return [
                    'tasks' => $paginatorService->getData(),
                    'search' => $paginatorService->getSearch(),
                    'currentPage' => $paginatorService->getCurrentPage(),
                    'currentLimit' => $paginatorService->getLimit(),
                    'firstPage' => $paginatorService->getUrlFirstPage(),
                    'lastPage' => $paginatorService->getUrlLastPage(),
                    'nextPage' => $paginatorService->getUrlNextPage(),
                    'previousPage' => $paginatorService->getUrlPreviousPage(),
                ];
            }
        );
        if (!is_array($parameters)) {
            $parameters = [];
        }
        if ($request->query->get('preview')) {
            return $this->render('components/task/_tasks.html.twig', $parameters);
        }

        return $this->render('task/tasks_todo.html.twig', $parameters);
    }
}
