<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Proxy;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['username' => 'Anonyme']);
        UserFactory::createMany(10);
        TaskFactory::createMany(200, function () use ($manager) {
            return [
                'owner' => UserFactory::random()
            ];
        });
        $manager->flush();
    }
}
