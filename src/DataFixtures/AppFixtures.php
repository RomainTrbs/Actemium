<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setUsername('christophe.cazeaux@actemium.com');
        $user->setRoles(["ROLE_SUPER_ADMIN"]);

        $encodedPassword = $this->passwordEncoder->encodePassword($user, 'christophe');
        $user->setPassword($encodedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}
