<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Poste;
use App\Entity\Status;
use App\Entity\Collaborateur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {


        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setUsername('christophe.cazeaux@actemium.com');
        $user->setRoles(["ROLE_SUPER_ADMIN"]);

        $encodedPassword = $this->passwordEncoder->hashPassword($user, 'christophe');
        $user->setPassword($encodedPassword);
        
        $poste1 = new Poste();
        $poste1->setNom('Technicien ELEC');
        $poste2 = new Poste();
        $poste2->setNom('Technicien AUTOM');

        $status1 = new Status();
        $status1->setNom('représentant');
        $status2 = new Status();
        $status2->setNom('collaborateur');

        $manager->persist($status1);
        $manager->flush();
        $manager->persist($status2);
        $manager->flush();
        $manager->persist($poste1);
        $manager->flush();
        $manager->persist($poste2);
        $manager->flush();

        $collaborateur = new Collaborateur();
        
        $collaborateur->setStatus($status1);
        $collaborateur->setPrenom('Super');
        $collaborateur->setNom('Admin');

        $collaborateur->setHrJour(7.4);
        $collaborateur->setHrSemaine(37);
        $collaborateur->setJourSemaine(5);

        

        $manager->persist($collaborateur);

        $manager->flush();

        $user->setCollaborateur($collaborateur);

        $manager->persist($user);

        $manager->flush();

        $collaborateur->setRepresentant($user);

        $manager->persist($collaborateur);

        $manager->flush();
    }
}
