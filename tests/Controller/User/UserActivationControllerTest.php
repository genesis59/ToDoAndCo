<?php

namespace App\Tests\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\UriSigner;

class UserActivationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;
    private EntityManager $entityManager;
    private UriSigner $uriSigner;
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @throws NotSupported
     */
    private function createUser(): User
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
        $this->client->followRedirect();
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'newuser@newuser.com']);
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $this->urlGenerator = static::getContainer()->get(UrlGeneratorInterface::class);
        $this->uriSigner = static::getContainer()->get(UriSigner::class);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->user = $userRepository->findOneByEmail('anonyme@anonyme.anonyme');
    }

    /**
     * @throws NotSupported
     */
    public function testActivateUserSuccess()
    {
        $user = $this->createUser();
        $url = $this->urlGenerator->generate(
            'user_activation',
            [
                'token' => $user->getActivationToken()->getToken(),
                'uuidUser' => $user->getUuid(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $uriSignee = $this->uriSigner->sign($url);
        $this->client->request('GET', $uriSignee);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains(
            '.alert-success',
            $this->translator->trans('app.flashes.activation.success')
        );
    }

    /**
     * @throws NotSupported
     */
    public function testActivateUserFailedUriNotSignee()
    {
        $user = $this->createUser();
        $url = $this->urlGenerator->generate(
            'user_activation',
            [
                'token' => $user->getActivationToken()->getToken(),
                'uuidUser' => $user->getUuid(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->client->request('GET', $url);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains(
            '.alert-danger',
            $this->translator->trans('app.flashes.activation.invalid_token')
        );
    }

    /**
     * @throws NotSupported
     */
    public function testActivateUserAlreadyLogged()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        $this->client->loginUser($user);
        $url = $this->urlGenerator->generate(
            'user_activation',
            [
                'token' => $user->getActivationToken()->getToken(),
                'uuidUser' => $user->getUuid(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $uriSignee = $this->uriSigner->sign($url);
        $this->client->request('GET', $uriSignee);
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            '.alert-success',
            $this->translator->trans('app.flashes.activation.already_activation')
        );
    }

    /**
     * @throws NotSupported
     */
    public function testActivateUserAlreadyActivated()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        $url = $this->urlGenerator->generate(
            'user_activation',
            [
                'token' => $user->getActivationToken()->getToken(),
                'uuidUser' => $user->getUuid(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $uriSignee = $this->uriSigner->sign($url);
        $this->client->request('GET', $uriSignee);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            '.alert-success',
            $this->translator->trans('app.flashes.activation.already_activation')
        );
    }

    public function testActivateUserTokenExpired()
    {
        $user = $this->createUser();
        $expiredDateTime = new \DateTimeImmutable('yesterday');
        $user->getActivationToken()->setExpiredAt($expiredDateTime);
        $this->entityManager->flush();
        $url = $this->urlGenerator->generate(
            'user_activation',
            [
                'token' => $user->getActivationToken()->getToken(),
                'uuidUser' => $user->getUuid(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $uriSignee = $this->uriSigner->sign($url);
        $this->client->request('GET', $uriSignee);
        $this->assertResponseRedirects('/users/new-activation');
        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            '.alert-danger',
            $this->translator->trans('app.flashes.activation.activation_time')
        );
    }
}
