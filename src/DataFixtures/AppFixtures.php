<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'username' => 'Anonyme',
            'email' => 'anonyme@anonyme.anonyme',
            'password' => 'Ceci12estuNpassw!ordpOuRan0nyme',
        ]);

        $admin = new User();
        $admin->setPassword('password');
        $admin->setUsername('admin');
        $admin->setEmail('admin@amdin.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        UserFactory::createMany(10);
        TaskFactory::createMany(200, function () {
            return [
                'owner' => UserFactory::random(),
            ];
        });
        $manager->flush();
    }
}
