<?php

namespace App\Tests\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserNewActivationControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private TranslatorInterface $translator;

    private $entityManager;
    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $userRepository->findOneByEmail('anonyme@anonyme.anonyme');
    }
    public function testNewActivationFormSubmissionIsAlreadyActivated()
    {
        $crawler = $this->client->request('GET', '/users/new-activation');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.security.new_activation.submit'))
            ->form([
                'new_activation' => [
                    'email' => 'anonyme@anonyme.anonyme'
                ]
            ])
        ;
        $this->client->submit($form);
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', $this->translator->trans('app.flashes.new_activation.already_activation'));
    }

    public function testNewActivationFormSubmissionBadEmail()
    {
        $crawler = $this->client->request('GET', '/users/new-activation');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.security.new_activation.submit'))
            ->form([
                'new_activation' => [
                    'email' => 'bad@email.com'
                ]
            ])
        ;
        $this->client->submit($form);
        $this->assertResponseRedirects('/users/new-activation', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', $this->translator->trans('app.flashes.new_activation.error'));
    }

    public function testNewActivationFormSubmission()
    {
        $user = new User();
        $user->setUsername('yourUsername');
        $user->setEmail('yourEmail@example.com');
        $user->setPassword('Password1234..');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/users/new-activation');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.security.new_activation.submit'))
            ->form([
                'new_activation' => [
                    'email' => 'yourEmail@example.com'
                ]
            ])
        ;
        $this->client->submit($form);
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', $this->translator->trans('app.flashes.new_activation.success'));
    }

    public function testUserIsAlreadyLogged()
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/users/new-activation');

        $this->assertResponseRedirects('/', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.default.index.sub_title'));
    }
}
