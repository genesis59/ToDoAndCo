<?php

namespace App\Tests\Controller\User;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserCreateControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }
    public function testGetUserCreatePage()
    {
        $this->client->request('GET','/users/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.user.create.sub_title'));
    }

    public function testUserCreate()
    {
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.user.create.submit'))
            ->form([
                    'user' => [
                        'username' => 'newuser',
                        'email' => 'newuser@newuser.com',
                        'password' => [
                            'first' => 'Password1234.',
                            'second' => 'Password1234.',
                        ],
                        'roles' => 'ROLE_ADMIN',
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertResponseRedirects('/login',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.security.login.sub_title'));
        $this->assertResponseIsSuccessful();
    }
}
