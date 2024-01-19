<?php

namespace App\Tests\Controller\Task;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskCreateControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private User $user;
    private TranslatorInterface $translator;
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $userRepository->findOneByEmail('anonyme@anonyme.anonyme');
    }

    public function testGetTaskCreatePageNotLogged()
    {
        $this->client->request('GET','/tasks/create');
        $this->assertResponseRedirects('/login',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.security.login.sub_title'));
    }

    public function testGetTaskCreatePage()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET','/tasks/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.task.create.sub_title'));
    }

    public function testPostTaskCreatePage()
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.task.create.create_button'))
            ->form([
                'task' => [
                    'title' => 'Nouvelle tâche',
                    'content' => 'Description de la tâche',
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks/todo',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.task.list.sub_title_task_todo'));
        $this->assertResponseIsSuccessful();
    }

    public function testPostTaskCreatePageContentTooSmall()
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.task.create.create_button'))
            ->form([
                    'task' => [
                        'title' => 'Nouvelle tâche',
                        'content' => 'D',
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertSelectorTextContains('.help-block', 'Le contenu de la tâche est trop court');
    }

    public function testPostTaskCreatePageContentTooLong()
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.task.create.create_button'))
            ->form([
                    'task' => [
                        'title' => 'Nouvelle tâche',
                        'content' => 'Dfffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
                        ffffffffffffDffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
                        fffffffffffDfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
                        ffffffffDfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
                        ffffDfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
                        DfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffDfff
                        ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffDfffffff
                        ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffDffffffffffff
                        fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffDffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffffffffffffffffffDffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffff
                        ffffffffffffffffffffffffffffffffffffffffffffffffDffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffff
                        ffffffffffffffffffffffffffffffffffffffffffDffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        fffffffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffff
                        ffffffffffffffffffffffffffffffffffDfffffffffffffffffffffffffffffffffffffffff',
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertSelectorTextContains('.help-block', 'Le contenu de la tâche est trop long');
    }
}
