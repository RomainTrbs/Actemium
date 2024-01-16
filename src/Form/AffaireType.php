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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AffaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ... other fields
            ->add('client')
            ->add('num_affaire')
            ->add('collaborateur', EntityType::class, [
                'class' => Collaborateur::class,
                'query_builder' => function (CollaborateurRepository $cr) {
                    return $cr->createQueryBuilder('c')
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
                'required' => false,
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
        ]);
    }
}

?>