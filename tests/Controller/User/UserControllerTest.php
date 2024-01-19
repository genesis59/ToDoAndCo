<?php

namespace App\Tests\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneByEmail('anonyme@anonyme.anonyme');
    }

    public function testGetUsersPageNotLogged()
    {
        $this->client->request('GET', '/users');
        $this->assertResponseRedirects('/',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseRedirects('/login',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.security.login.sub_title'));
    }

    public function testGetUsersPageLoggedButNotRoleAdmin()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/users');
        $this->assertResponseRedirects('/',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.default.index.sub_title'));
    }

    public function testUserListPageIsSuccessful()
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.user.list.sub_title'));
    }
    public function testPaginatorError(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/users?limit=10&page=y&search=');
        $this->assertResponseRedirects('/', Response::HTTP_FOUND);
    }

    public function testPreviewParameter(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/users', ['preview' => true]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
