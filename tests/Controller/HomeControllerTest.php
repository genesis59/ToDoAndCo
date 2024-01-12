<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeControllerTest extends WebTestCase
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


    public function testHomePageNotLogged(){
        $this->client->request('GET','/');
        $this->assertResponseRedirects('/login',Response::HTTP_FOUND);
    }

    public function testHomePageLoggedAsUser(){
        $this->client->loginUser($this->user);
        $this->client->request('GET','/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.default.index.sub_title'));
    }
}
