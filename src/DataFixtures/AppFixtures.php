<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setUsername('christophe.cazeaux@actemium.com');
        $user->setRoles("['ROLE_SUPER_ADMIN']");

        $encodedPassword = $this->passwordEncoder->encodePassword($user, 'christophe');
        $user->setPassword($encodedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}
