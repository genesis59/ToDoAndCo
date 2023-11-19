<?php

namespace App\Controller\Task;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskDeleteController extends AbstractController
{
    #[Route(path: '/tasks/{uuid}/delete', name: 'task_delete')]
    public function __invoke(Request $request, Task $task, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $routeName = 'task_list_todo';
        if ($task->isDone()) {
            $routeName = 'task_list_finished';
        }
        if (!$this->isGranted('TASK_DELETE', $task)) {
            $this->addFlash('error', $translator->trans('app.flashes.task.user_not_authorized_to_delete'));

            return $this->redirectToRoute($routeName);
        }
        $em = $managerRegistry->getManager();
        $em->remove($task);
        $em->flush();
        $this->addFlash('success', $translator->trans('app.flashes.task.deleted'));

        return $this->redirectToRoute($routeName);
    }
}
