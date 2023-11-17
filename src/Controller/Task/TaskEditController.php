<?php

namespace App\Controller\Task;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskEditController extends AbstractController
{
    #[Route(path: '/tasks/{uuid}/edit', name: 'task_edit')]
    public function __invoke(Task $task, Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $response = $this->redirectToRoute('task_list_todo');
        if ($task->isDone()) {
            $response = $this->redirectToRoute('task_list_finished');
        }
        if (!$this->isGranted('TASK_EDIT', $task)) {
            $this->addFlash('error', $translator->trans('app.flashes.task.user_not_authorized_to_edit'));

            return $response;
        }
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('success', $translator->trans('app.flashes.task.updated'));

            return $response;
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }
}
