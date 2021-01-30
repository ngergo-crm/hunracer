<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        UserFactory::new(['roles' => ['ROLE_SUPER_ADMIN'], 'isEnabled' => true])->create();
        UserFactory::new()->createMany(10);
    }
}
