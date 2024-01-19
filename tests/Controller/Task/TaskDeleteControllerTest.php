<?php

namespace App\Tests\Controller\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskDeleteControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private TranslatorInterface $translator;
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;
    private $entityManager;
    private Task $taskIsDone;
    private Task $taskIsNotDone;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->taskIsDone = $this->taskRepository->findOneBy(['isDone' => true]);
        $this->taskIsNotDone = $this->taskRepository->findOneBy(['isDone' => false]);
    }

    public function testGetTaskCreatePageNotLogged()
    {
        $this->client->request('GET', '/tasks/' . $this->taskIsDone->getUuid() . '/delete');
        $this->assertResponseRedirects('/login',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.security.login.sub_title'));
    }

    public function testTaskIsDoneDeletionByOwner()
    {
        $this->client->loginUser($this->taskIsDone->getOwner());
        $this->client->request('GET', '/tasks/' . $this->taskIsDone->getUuid() . '/delete');
        $this->assertResponseRedirects('/tasks/finished',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', $this->translator->trans('app.flashes.task.deleted'));
        $task = $this->taskRepository->findOneBy(['uuid' => $this->taskIsDone->getUuid()]);
        $this->assertNull($task);
    }

    public function testTaskIsNotDoneDeletionByOwner()
    {
        $this->client->loginUser($this->taskIsNotDone->getOwner());
        $this->client->request('GET', '/tasks/' . $this->taskIsNotDone->getUuid() . '/delete');
        $this->assertResponseRedirects('/tasks/todo',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', $this->translator->trans('app.flashes.task.deleted'));
        $task = $this->taskRepository->findOneBy(['uuid' => $this->taskIsNotDone->getUuid()]);
        $this->assertNull($task);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testTaskDeletionByNotOwner()
    {
        $otherUser = $this->userRepository->findRandomUserNotAnonymeAndNotEqualTo($this->taskIsDone->getOwner()->getId());
        $this->client->loginUser($otherUser);
        $this->client->request('GET', '/tasks/' . $this->taskIsDone->getUuid() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', $this->translator->trans('app.flashes.task.user_not_authorized_to_delete'));
        $task = $this->taskRepository->findOneBy(['uuid' => $this->taskIsDone->getUuid()]);
        $this->assertNotNull($task);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testdeleteAnonymeTaskWithAdmin()
    {
        $user = $this->userRepository->findRandomUserNotAnonymeAndNotEqualTo();
        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->flush();
        $this->client->loginUser($user);
        $anonymeTask = $this->taskRepository->findOneBy(['owner' => 1]);
        $uuidTask = $anonymeTask->getUuid();
        $this->assertEquals('anonyme@anonyme.anonyme',$anonymeTask->getOwner()->getEmail());
        $this->client->request('GET', '/tasks/' . $anonymeTask->getUuid() . '/delete');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', $this->translator->trans('app.flashes.task.deleted'));
        $task = $this->taskRepository->findOneBy(['uuid' => $uuidTask]);
        $this->assertNull($task);
    }
}
