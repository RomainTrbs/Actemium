<?php

namespace App\Form;

use App\Entity\Affaire;
use App\Entity\Collaborateur;
use Symfony\Component\Form\AbstractType;
use App\Repository\CollaborateurRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AffaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userId = $options['userId'];
        $isSuperAdmin = $options['isSuperAdmin'];
        $builder
            ->add('client')
            ->add('num_affaire', TextType::class, [
                'required' => true
            ])
            ->add('designation')
            ->add('nbre_heure')
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('heure_passe', null, [
                'required' => false,
            ])           
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('nbre_jour_fractionnement')
            ->add('pourcent_reserve');
            if($isSuperAdmin){
            $builder
            ->add('collaborateur', EntityType::class, [
                'multiple' => true,
                'class' => Collaborateur::class,
                'query_builder' => function (CollaborateurRepository $cr) use ($userId) {
                    return $cr->createQueryBuilder('c')
                    ->andWhere('c.status = :statusId')
                    ->setParameter('statusId', 2)
                    ->orderBy('c.nom', 'ASC');
                },
                'choice_label' => function ($collaborateur) {
                    return $collaborateur->getNom() . ' ' . $collaborateur->getPrenom();
                },
            ]);
            }else{
                $builder
                ->add('collaborateur', EntityType::class, [
                    'multiple' => true,
                    'class' => Collaborateur::class,
                    'query_builder' => function (CollaborateurRepository $cr) use ($userId) {
                        return $cr->createQueryBuilder('c')
                            ->andWhere('c.representant = :representantId')
                            ->setParameter('representantId', $userId)
                            ->andWhere('c.status = :statusId')  // Affiche uniquement les collaborateurs avec le statut "collaborateur"
                            ->setParameter('statusId', 2) // Changez 'collaborateur' par la valeur réelle pour le statut collaborateur
                            ->orderBy('c.nom', 'ASC');
                    },
                    'choice_label' => function ($collaborateur) {
                        return $collaborateur->getNom() . ' ' . $collaborateur->getPrenom();
                    },
                ]);
            }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Affaire::class,
            'userId' => null, // Define the userId option with a default value
            'isSuperAdmin' => null, 
        ]);
    }
}
