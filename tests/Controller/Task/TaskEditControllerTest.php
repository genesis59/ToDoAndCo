<?php

namespace App\Tests\Controller\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskEditControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;
    private TaskRepository $taskRepository;
    private Task $taskIsDone;
    private Task $taskIsNotDone;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskIsDone = $this->taskRepository->findOneBy(['isDone' => true]);
        $this->taskIsNotDone = $this->taskRepository->findOneBy(['isDone' => false]);
    }

    public function testGetTaskCreatePageNotLogged()
    {
        $this->client->request('GET', '/tasks/' . $this->taskIsDone->getUuid() . '/edit');
        $this->assertResponseRedirects('/login',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testTaskIsNotDoneEditByOwner()
    {
        $this->client->loginUser($this->taskIsNotDone->getOwner());
        $crawler = $this->client->request('GET', '/tasks/' . $this->taskIsNotDone->getUuid() . '/edit');
        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.task.edit.edit_button'))
            ->form([
                    'task' => [
                        'title' => 'Autre titre',
                        'content' => 'Autre description',
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks/todo',Response::HTTP_FOUND);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', $this->translator->trans('app.flashes.task.updated'));
        $task = $this->taskRepository->findOneBy(['uuid' => $this->taskIsNotDone->getUuid()]);
        $this->assertSame('Autre titre',$task->getTitle());
        $this->assertSame('Autre description',$task->getContent());
    }

    public function testTaskIsDoneEditByOwner()
    {
        $this->client->loginUser($this->taskIsDone->getOwner());
        $crawler = $this->client->request('GET', '/tasks/' . $this->taskIsDone->getUuid() . '/edit');

        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.task.edit.edit_button'))
            ->form([
                    'task' => [
                        'title' => 'Autre titre',
                        'content' => 'Autre description',
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks/finished',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', $this->translator->trans('app.flashes.task.updated'));
        $task = $this->taskRepository->findOneBy(['uuid' => $this->taskIsDone->getUuid()]);
        $this->assertSame('Autre titre',$task->getTitle());
        $this->assertSame('Autre description',$task->getContent());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testTaskDeletionByNotOwner()
    {
        $otherUser = $this->userRepository->findRandomUserNotAnonymeAnNotEqualTo($this->taskIsDone->getOwner()->getId());
        $this->client->loginUser($otherUser);
        $this->client->request('GET', '/tasks/' . $this->taskIsNotDone->getUuid() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', $this->translator->trans('app.flashes.task.user_not_authorized_to_edit'));
    }
}
