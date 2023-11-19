<?php

namespace App\Controller\Task;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskToggleController extends AbstractController
{
    #[Route(path: '/tasks/{uuid}/toggle', name: 'task_toggle')]
    public function __invoke(Task $task, ManagerRegistry $managerRegistry, TranslatorInterface $translator): Response
    {
        $task->toggle(!$task->isDone());
        $routeName = 'task_list_finished';
        $message = 'app.flashes.task.is_not_done';
        if ($task->isDone()) {
            $message = 'app.flashes.task.is_done';
            $routeName = 'task_list_todo';
        }
        $managerRegistry->getManager()->flush();
        $this->addFlash('success', $translator->trans($message, ['%task%' => $task->getTitle()]));

        return $this->redirectToRoute($routeName);
    }
}
