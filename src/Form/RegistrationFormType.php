<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Doctrine\ORM\EntityRepository; // Add this line for EntityRepository

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $options['userId'];
        $builder
            ->add('username')
            ->add('collaborateur', EntityType::class, [
                'class' => 'App\Entity\Collaborateur',
                'choice_label' => function ($collaborateur) {
                    return $collaborateur->getNom() . ' ' . $collaborateur->getPrenom();
                },
                'query_builder' => function (EntityRepository $er) use ($userId) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('App\Entity\User', 'u', 'WITH', 'u.collaborateur = c.id')
                        ->where('u.id IS NULL OR u.id = :userId')
                        ->setParameter('userId', $userId);
                },
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez le mot de passe'],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'userId' => null,
        ]);
    }
}
