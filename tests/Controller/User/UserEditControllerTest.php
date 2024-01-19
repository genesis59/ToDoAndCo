<?php

namespace App\Tests\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserEditControllerTest extends WebTestCase
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

    /**
     * @throws NonUniqueResultException
     */
    public function testGetUsersPageNotLogged()
    {
        $userNotAnonyme = $this->userRepository->findRandomUserNotAnonymeAndNotEqualTo();
        $this->client->request('GET', '/users/' . $userNotAnonyme->getUuid() . '/edit');
        $this->assertResponseRedirects('/',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseRedirects('/login',Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', $this->translator->trans('app.twig.page.security.login.sub_title'));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testEditUserAdmin()
    {
        $user = $this->userRepository->findOneBy(['id' => 2]);
        $this->client->loginUser($user);
        $userNotAnonyme = $this->userRepository->findRandomUserNotAnonymeAndNotEqualTo();
        $crawler = $this->client->request('GET', '/users/' . $userNotAnonyme->getUuid() . '/edit');
        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.user.edit.submit'))
            ->form([
                    'user_edit' => [
                        'username' => 'Autreusername',
                        'email' => 'autre@autre.com',
                        'roles' => 'ROLE_ADMIN'
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testEditUserNotAdmin()
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->client->loginUser($this->user);

        $userNotAnonyme = $this->userRepository->findRandomUserNotAnonymeAndNotEqualTo();
        $crawler = $this->client->request('GET', '/users/' . $userNotAnonyme->getUuid() . '/edit');


        $form = $crawler
            ->selectButton($this->translator->trans('app.twig.page.user.edit.submit'))
            ->form([
                    'user_edit' => [
                    ]
                ]
            );
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
