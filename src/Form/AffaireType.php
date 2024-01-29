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
        $builder
            // ... other fields
            ->add('client')
            ->add('num_affaire', TextType::class, [
                'required' => true
            ])
            ->add('collaborateurs', EntityType::class, [
                'class' => Collaborateur::class,
                'multiple' => true,
                'query_builder' => function (CollaborateurRepository $cr) use ($userId) {
                    return $cr->createQueryBuilder('c')
                        ->andWhere('c.representant = :representantId')
                        ->setParameter('representantId', $userId)
                        ->orderBy('c.nom', 'ASC');
                },
                'choice_label' => function ($collaborateur) {
                    return $collaborateur->getNom() . ' ' . $collaborateur->getPrenom();
                },
            ])
            ->add('designation')
            ->add('nbre_heure')
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('heure_passe')
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('nbre_jour_fractionnement')
            ->add('pourcent_reserve');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Affaire::class,
            'userId' => null, // Define the userId option with a default value
        ]);
    }
}
