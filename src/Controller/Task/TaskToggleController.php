<?php

namespace App\Controller\Task;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskToggleController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    #[Route(path: '/tasks/{uuid}/toggle', name: 'task_toggle')]
    public function __invoke(
        Task $task,
        ManagerRegistry $managerRegistry,
        TranslatorInterface $translator,
        TagAwareCacheInterface $cache
    ): Response
    {
        $routeName = 'task_list_todo';
        $message = 'app.flashes.task.is_not_done';
        if ($task->isDone()) {
            $message = 'app.flashes.task.is_done';
            $routeName = 'task_list_finished';
        }

        if ($this->getUser() !== $task->getOwner()) {
            $this->addFlash('error', $translator->trans('app.flashes.task.error_toggle'));
            return $this->redirectToRoute($routeName);
        }
        $cache->invalidateTags(['tasksFinishedCache']);
        $cache->invalidateTags(['tasksTodoCache']);
        $task->toggle(!$task->isDone());

        $managerRegistry->getManager()->flush();
        $this->addFlash('success', $translator->trans($message, ['%task%' => $task->getTitle()]));

        return $this->redirectToRoute($routeName);
    }
}
