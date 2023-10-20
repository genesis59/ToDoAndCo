<?php

namespace App\DataFixtures;

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
        UserFactory::createMany(10);
        TaskFactory::createMany(200, function () {
            return [
                'owner' => UserFactory::random(),
            ];
        });
        $manager->flush();
    }
}
