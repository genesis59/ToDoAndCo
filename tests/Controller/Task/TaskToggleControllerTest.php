<?php

namespace App\Tests\Controller\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskToggleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TaskRepository $taskRepository;
    private Task $taskIsDone;
    private Task $taskIsNotDone;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->taskIsDone = $this->taskRepository->findOneBy(['isDone' => true]);
        $this->taskIsNotDone = $this->taskRepository->findOneBy(['isDone' => false]);
    }
    public function testStartWithTaskIsDone(): void
    {
        $taskUuid = $this->taskIsDone->getUuid();
        $this->client->loginUser($this->taskIsDone->getOwner());
        $this->assertTrue($this->taskIsDone->isDone());
        $this->client->request('GET', '/tasks/' . $taskUuid . '/toggle');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $checkTaskIsDone = $this->taskRepository->findOneBy(['uuid' => $taskUuid]);
        $this->assertNotNull($checkTaskIsDone, 'Task not found');
        $this->assertFalse($checkTaskIsDone->isDone());
    }

    public function testStartWithTaskIsNotDone(): void
    {
        $taskUuid = $this->taskIsNotDone->getUuid();
        $this->client->loginUser($this->taskIsNotDone->getOwner());
        $this->assertFalse($this->taskIsNotDone->isDone());
        $this->client->request('GET', '/tasks/' . $taskUuid . '/toggle');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $checkTaskIsDone = $this->taskRepository->findOneBy(['uuid' => $taskUuid]);
        $this->assertNotNull($checkTaskIsDone, 'Task not found');
        $this->assertTrue($checkTaskIsDone->isDone());
    }
}
