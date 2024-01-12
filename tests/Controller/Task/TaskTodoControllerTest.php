<?php

namespace App\Tests\Controller\Task;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;


class TaskTodoControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;

    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $userRepository->findOneByEmail('anonyme@anonyme.anonyme');
    }

    public function testPaginatorError(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/tasks/todo?limit=6&page=A&search=');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', $this->translator->trans('app.exceptions.bad_request_http_exception_page'));
    }

    public function testPreviewParameter(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/tasks/todo', ['preview' => true]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
