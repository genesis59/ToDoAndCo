<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Paginator\PaginatorService;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly TaskRepository $taskRepository,
        private readonly TranslatorInterface $translator,
        private readonly PaginatorService $paginatorService
    ) {
    }

    #[Route(path: '/tasks/todo', name: 'task_list_todo')]
    public function todoList(Request $request): Response
    {
        $paginationError = $this->paginatorService->create(
            $this->taskRepository,
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
            'tasks' => $this->paginatorService->getData(),
            'currentPage' => $this->paginatorService->getCurrentPage(),
            'firstPage' => $this->paginatorService->getUrlFirstPage(),
            'lastPage' => $this->paginatorService->getUrlLastPage(),
            'nextPage' => $this->paginatorService->getUrlNextPage(),
            'previousPage' => $this->paginatorService->getUrlPreviousPage(),
        ]);
    }

    #[Route(path: '/tasks/finished', name: 'task_list_finished')]
    public function finishedList(Request $request): Response
    {
        $paginationError = $this->paginatorService->create(
            $this->taskRepository,
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
                'tasks' => $this->paginatorService->getData(),
                'currentPage' => $this->paginatorService->getCurrentPage(),
                'firstPage' => $this->paginatorService->getUrlFirstPage(),
                'lastPage' => $this->paginatorService->getUrlLastPage(),
                'nextPage' => $this->paginatorService->getUrlNextPage(),
                'previousPage' => $this->paginatorService->getUrlPreviousPage(),
            ]
        );
    }

    #[Route(path: '/tasks/create', name: 'task_create')]
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $task->setOwner($user);
            $task->setUuid(Uuid::v4());
            $em = $this->managerRegistry->getManager();
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', $this->translator->trans('app.flashes.task.created'));

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/tasks/{uuid}/edit', name: 'task_edit')]
    public function edit(Task $task, Request $request): Response
    {
        if (!$this->isGranted('TASK_EDIT', $task)) {
            $this->addFlash('error', $this->translator->trans('app.flashes.task.user_not_authorized_to_edit'));

            return $this->redirect($request->headers->get('referer'));
        }
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();
            $this->addFlash('success', $this->translator->trans('app.flashes.task.updated'));

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route(path: '/tasks/{uuid}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task): Response
    {
        $task->toggle(!$task->isDone());
        $this->managerRegistry->getManager()->flush();

        $this->addFlash('success', $this->translator->trans('app.flashes.task.is_done', ['%task%' => $task->getTitle()]));

        return $this->redirectToRoute('task_list_todo');
    }

    #[Route(path: '/tasks/{uuid}/delete', name: 'task_delete')]
    public function deleteTask(Request $request, Task $task): Response
    {
        if (!$this->isGranted('TASK_DELETE', $task)) {
            $this->addFlash('error', $this->translator->trans('app.flashes.task.user_not_authorized_to_delete'));

            return $this->redirect($request->headers->get('referer'));
        }
        $em = $this->managerRegistry->getManager();
        $em->remove($task);
        $em->flush();
        $this->addFlash('success', $this->translator->trans('app.flashes.task.deleted'));

        return $this->redirect($request->headers->get('referer'));
    }
}
