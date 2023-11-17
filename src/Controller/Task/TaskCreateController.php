<?php

namespace App\Controller\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskCreateController extends AbstractController
{
    #[Route(path: '/tasks/create', name: 'task_create')]
    public function __invoke(Request $request, ManagerRegistry $managerRegistry, TranslatorInterface $translator): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $task->setOwner($user);
            $task->setUuid(Uuid::v4());
            $em = $managerRegistry->getManager();
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', $translator->trans('app.flashes.task.created'));

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }
}
