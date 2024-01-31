<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Poste;
use App\Entity\Status;
use App\Entity\Affaire;
use App\Entity\Collaborateur;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollaborateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('prenom')
        ->add('nom')
        ->add('hr_jour')
        ->add('hr_semaine')
        ->add('jour_semaine')
        ->add('representant', EntityType::class, [
            'class' => User::class,
            'query_builder' => function (UserRepository $cr) {
                return $cr->createQueryBuilder('c')
                    ->leftJoin('c.collaborateur', 'col') // Assuming Collaborateur entity has a 'collaborateur' property in User entity
                    ->leftJoin('col.status', 's')
                    ->where('s.nom != :statusNom OR s.nom IS NULL') // Exclude users with 'collaborateur' postes
                    ->setParameter('statusNom', 'collaborateur')
                    ->orderBy('c.username', 'ASC'); // Assuming User entity has a 'username' property
            },
            'choice_label' => function ($representant) {
                $representant = $representant->getCollaborateur();
                $representant = $representant->getPrenom() . ' ' . $representant->getNom();
                return $representant; // Assuming User entity has a 'getUsername()' method
            },
        ])
        ->add('poste', EntityType::class, [
            'class' => 'App\Entity\Poste',
            'choice_label' => function ($poste) {
                return $poste->getNom();
            },                    
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collaborateur::class,
        ]);
    }
}
