<?php

namespace App\Tests\Security;

use App\Controller\SecurityController;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginFormAuthenticatorTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;
    private UserRepository $userRepository;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testSuccessLogin()
    {
        $userNotAnonyme = $this->userRepository->findRandomUserNotAnonymeAndNotEqualTo();
        $userNotAnonyme->setActivated(true);

        $this->entityManager->persist($userNotAnonyme);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->filter('form')->form([
            'email' => $userNotAnonyme->getEmail(),
            'password' => 'password',
            '_csrf_token' => $crawler->filter('input[name="_csrf_token"]')->attr('value'),
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertRouteSame('homepage');
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testNotActivatedLogin()
    {
        $userNotAnonyme = $this->userRepository->findRandomUserNotAnonymeAndNotEqualTo();
        $userNotAnonyme->setActivated(false);

        $this->entityManager->persist($userNotAnonyme);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->filter('form')->form([
            'email' => $userNotAnonyme->getEmail(),
            'password' => 'password',
            '_csrf_token' => $crawler->filter('input[name="_csrf_token"]')->attr('value'),
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-info',$this->translator->trans('app.twig.page.security.login.new_activation'));
    }

    public function testFailLogin()
    {


        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->filter('form')->form([
            'email' => 'email@inconnu.fr',
            'password' => 'password11',
            '_csrf_token' => $crawler->filter('input[name="_csrf_token"]')->attr('value'),
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger',$this->translator->trans('Invalid credentials.',[],'security'));
    }

    public function testLogout(): void
    {
        $this->client->loginUser($this->userRepository->findOneBy([]));
        $this->client->request('GET', '/logout');
        $this->client->followRedirect();
        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.security.login.sub_title'));
    }

    public function testMethodeQuiNeDoitPasAppelerLogout(): void
    {
        // Créer un mock de votre classe ou service
        $mockVotreClasse = $this->getMockBuilder(SecurityController::class)
            ->disableOriginalConstructor()
            ->getMock();

        // S'assurer que la méthode logout ne sera jamais appelée
        $mockVotreClasse->expects($this->never())
            ->method('logout');


    }
}
