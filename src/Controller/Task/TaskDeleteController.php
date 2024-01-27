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

class TaskDeleteController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    #[Route(path: '/tasks/{uuid}/delete', name: 'task_delete')]
    public function __invoke(
        Task $task,
        TranslatorInterface $translator,
        ManagerRegistry $managerRegistry,
        TagAwareCacheInterface $cache
    ): Response
    {
        $routeName = 'task_list_todo';
        if ($task->isDone()) {
            $routeName = 'task_list_finished';
        }
        if (!$this->isGranted('TASK_DELETE', $task)) {
            $this->addFlash('error', $translator->trans('app.flashes.task.user_not_authorized_to_delete'));

            return $this->redirectToRoute($routeName);
        }
        $cache->invalidateTags(['tasksFinishedCache']);
        $cache->invalidateTags(['tasksTodoCache']);
        $em = $managerRegistry->getManager();
        $em->remove($task);
        $em->flush();
        $this->addFlash('success', $translator->trans('app.flashes.task.deleted'));

        return $this->redirectToRoute($routeName);
    }
}
