<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Collaborateur;
use App\Form\CollaborateurType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CollaborateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class RegistrationController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {        
        $user = $this->getUser();
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $users = $userRepository->findAll();
        } elseif (in_array('ROLE_ADMIN', $user->getRoles())) {
            $users = $userRepository->findAllByRepresentant($user->getId());
        } else {
            // If the user has other roles, retrieve only the current user
            $users = [$user];
        }


        return $this->render('registration/index.html.twig',[
            'users' => $users,
        ]);
    }

    #[Route('/type-of-user', name: 'app_choose')]
    public function choose(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        return $this->render('registration/choose.html.twig');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $roles = ['ROLE_USER'];
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_user');
        }

        return $this->render('registration/new.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register-admin', name: 'app_register_admin')]
    public function registerAdmin(Request $request, UserRepository $userRepository, CollaborateurRepository $collaborateurRepository,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, StatusRepository $statusRepository, PersistenceManagerRegistry $doctrine): Response
    {
        $user = new User();

        $collaborateur = new Collaborateur();
                
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $form2 = $this->createForm(CollaborateurType::class, $collaborateur);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password



            $status = $statusRepository->Find(1);
            $collaborateur->setStatus($status);

            $collaborateur->setNom($form2->get('nom')->getData());
            $collaborateur->setPrenom($form2->get('prenom')->getData());
            $collaborateur->setHrJour(7.4);
            $collaborateur->setHrSemaine(37);            
            $collaborateur->setJourSemaine(5);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($collaborateur);
            $entityManager->flush();

            $freshCollaborateur = $doctrine->getRepository(Collaborateur::class)->find($collaborateur->getId());

            $user->setCollaborateur($freshCollaborateur);

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $roles = ['ROLE_ADMIN'];
            $user->setRoles($roles);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $freshUser = $userRepository->findOneBy([], ['id' => 'DESC']);

            // Effectuez une requête pour récupérer le dernier utilisateur créé
    
            $collaborateur->setRepresentant($freshUser);

            $entityManager->persist($collaborateur);
            $entityManager->flush();


            return $this->redirectToRoute('app_user');
        }

        // Effectuez une requête pour récupérer le dernier utilisateur créé

        return $this->render('registration/new_admin.html.twig', [
            'registrationForm' => $form->createView(),
            'collaborateurForm' => $form2->createView(),
            // 'freshUser' => $freshUser,
            // 'freshCollaborateur' => $freshCollaborateur
        ]);
    }

    #[Route('/{id}', name: 'app_show_user')]
    public function showUser(User $user)
    {
        return $this->render('registration/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/uprole/{id}', name: 'app_uprole_user')]
    public function upRole(User $user, PersistenceManagerRegistry $doctrine)
    {
        $roles = $user->getRoles();
        if(in_array('ROLE_ADMIN', $roles)){
            $givenRole = ["ROLE_SUPER_ADMIN"];
            $user->setRoles($givenRole);
        }else{
            $givenRole = ["ROLE_ADMIN"];
            $user->setRoles($givenRole);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_show_user', ['id' => $user->getId()]);
    }

    #[Route('/downrole/{id}', name: 'app_downrole_user')]
    public function downRole(User $user, PersistenceManagerRegistry $doctrine)
    {
        $roles = $user->getRoles();
        if(in_array('ROLE_SUPER_ADMIN', $roles)){
            $givenRole = ["ROLE_ADMIN"];
            $user->setRoles($givenRole);
        }else{
            $givenRole = ["ROLE_USER"];
            $user->setRoles($givenRole);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_show_user', ['id' => $user->getId()]);
    }

    #[Route('/edit/{id}', name: 'app_edit_user')]
    public function edit(Request $request, User $user, PersistenceManagerRegistry $doctrine, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
            ])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage du mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            
            // Mettre à jour l'utilisateur
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_show_user', ['id' => $user->getId()]);
        }
    
        return $this->render('registration/edit.html.twig', [
            'user' => $user,
            'registrationForm' => $form->createView(),
        ]);
    }    
   

    #[Route('/delete/{id}', name: 'app_delete_user')]
    public function delete(Request $request, User $user, PersistenceManagerRegistry $doctrine, CollaborateurRepository $collaborateurRepository): Response
    {
        // Fetch all Collaborateurs
        $collaborateurs = $collaborateurRepository->findAllByRepresentant($user->getId());
        
        // Check if the user has the ROLE_SUPER_ADMIN role
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Get the associated Collaborateur
            $associatedCollaborateur = $user->getCollaborateur();
            
            // Loop through all Collaborateurs and remove the representant association
            foreach ($collaborateurs as $collaborateur) {
                $collaborateur->setRepresentant(null);                                
            }
            
            // If the CSRF token is valid, delete the User and its associated Collaborateur
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($user);
                $entityManager->remove($associatedCollaborateur);
                $entityManager->flush();
            }
        } else {
            // If the CSRF token is valid, delete only the User
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }


        return $this->redirectToRoute('app_user');
    }


}
