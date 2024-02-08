<?php

namespace App\Controller;

use DateTime;
use LogicException;
use App\Entity\User;
use App\Entity\Affaire;
use App\Form\AffaireType;
use App\Entity\Collaborateur;
use App\Repository\UserRepository;
use App\Controller\AffaireController;

use App\Repository\AffaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CollaborateurRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class AffaireController extends AbstractController
{
    #[Route('/', name: 'affaire_index')]
    public function index(AffaireRepository $affaireRepository, UserRepository $userRepository,
     CollaborateurRepository $collaborateurRepository, Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser();        
        $userId = $user->getId();
        
        $role = $user->getRoles();
        if(in_array('ROLE_SUPER_ADMIN', $role)){
            $isSuperAdmin = true;
        }else{
            $isSuperAdmin = false;
        }

        if($isSuperAdmin){
            $collaborateurs = $collaborateurRepository->findAllWithAffairesOnly();
        }else{
            $collaborateurs = $collaborateurRepository->findAllWithAffaires($user->getId());
        }
        $affaires = $affaireRepository->findAllByUser($user->getId());
        $session = $request->getSession();
        $session->start();
        $d2 = new DateTime ;
        $d2->modify('+365 days');

        // Retrieve stored dates from the session or use default values
        $firstDate = $session->get('firstDate', new DateTime);
        $lastDate = $session->get('lastDate', $d2);
        $filtre = $session->get('filter', null);

        $value = $session->get('collaborateur', []);
        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
            $form2 = $this->createFormBuilder()
            ->add('collaborateur', EntityType::class, [
                'multiple' => true,
                'required' => false,
                'class' => Collaborateur::class,
                'query_builder' => function (CollaborateurRepository $cr) use ($userId) {
                    return $cr->createQueryBuilder('a')
                    ->andWhere('a.status = :statusId')
                    ->setParameter('statusId', 2)
                    ->orderBy('a.nom', 'ASC');
                },
                'choice_label' => function ($collaborateur) {
                    return $collaborateur->getNom() . ' ' . $collaborateur->getPrenom();
                }
            ])
            ->add('selectAll', CheckboxType::class, [
                'mapped' => false, // Cette case à cocher n'est pas liée à une propriété de l'entité
                'label' => 'Sélectionner tous les collaborateurs',
                'required' => false, // L'utilisateur n'est pas obligé de la cocher
                'attr' => ['class' => 'select-all-checkbox'],
            ])
            ->add('validate', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']])
            ->getForm();
        }
        else{
            $form2 = $this->createFormBuilder()
            ->add('collaborateur', EntityType::class, [
                'multiple' => true,
                'required' => false,
                'class' => Collaborateur::class,
                'query_builder' => function (CollaborateurRepository $cr) use ($userId) {
                    return $cr->createQueryBuilder('c')
                        ->andWhere('c.representant = :representantId')
                        ->setParameter('representantId', $userId)
                        ->andWhere('c.status = :statusId')
                        ->setParameter('statusId', 2)
                        ->orderBy('c.nom', 'ASC');
                },
                'choice_label' => function ($collaborateur) {
                    return $collaborateur->getNom() . ' ' . $collaborateur->getPrenom();
                }
            ])
            ->add('selectAll', CheckboxType::class, [
                'mapped' => false, // Cette case à cocher n'est pas liée à une propriété de l'entité
                'label' => 'Sélectionner tous les collaborateurs',
                'required' => false, // L'utilisateur n'est pas obligé de la cocher
                'attr' => ['class' => 'select-all-checkbox'],
            ])
            ->add('validate', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']])
            ->getForm();
        }

        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            if ($form2->get('validate')->isClicked()) {
                $data = $form2->getData();
                $collaborateurChoisi = $data['collaborateur'];

                if ($form2->get('selectAll')->getData() === true) {
                    $collaborateurs = $form2->get('collaborateur')->getConfig()->getOption('choices');
                    $collaborateurChoisi = is_array($collaborateurs) ? array_keys($collaborateurs) : [];
                }
                // Store the filter and collaborator values in the session
                $session->set('collaborateur', $collaborateurChoisi);
                
                $session->set('filter', null);

                // Redirect the user to the same route with the parameters
                return $this->redirectToRoute('affaire_index');
            }       
        }
        if(($session->get('collaborateur')) != null ){
            $collaborateurGroupe = [];
            foreach($session->get('collaborateur') as $collaborateur){
                array_push($collaborateurGroupe, $collaborateur->getId());                              
            }
            $collaborateursChoisi = $collaborateurRepository->findAllInArray($collaborateurGroupe);
        }
        

        // Create the form with the stored values
        $form = $this->createFormBuilder(['firstDate' => $firstDate, 'lastDate' => $lastDate])
            ->add('firstDate', DateType::class)
            ->add('lastDate', DateType::class)
            ->add('validate', SubmitType::class, [
                'label' => 'Valider',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validate')->isClicked()) {
                // The "Validate" button was clicked
                $data = $form->getData();

                $firstDate = $data['firstDate'];
                $lastDate = $data['lastDate'];

                // Store the dates in the session
                $session->set('firstDate', $firstDate);
                $session->set('lastDate', $lastDate);

                // Redirect the user to the same route with parameters dd and df
                return $this->redirectToRoute('affaire_index');
            }

            // Handle other form submission logic here
        }

        if ($request->query->has('filter')) {

            $filter = $request->query->get('filter');
            
            switch ($filter) {
                case 'client':
                    $session->set('filter', 'client');
                    break;
                case 'date':
                    $session->set('filter', 'date');
                    break;
                default:
                    // Gérer le cas par défaut ici, par exemple, affecter une valeur par défaut à $affaires
                    $filter = null;
                    break;
            }
        }
        
        $filtre = $session->get('filter');
        if($filtre != null){
            if($filtre == 'client'){
                if(isset($collaborateursChoisi)){
                    $affaires = $affaireRepository->findAllOngoingByClient($userId, $collaborateursChoisi, $isSuperAdmin);
                }else{
                    $affaires = $affaireRepository->findAllOngoingByClient($userId, $collaborateurs, $isSuperAdmin);
                }
                $filter = 'client';
            }else if($filtre == 'date'){
                if(isset($collaborateursChoisi)){
                    $affaires = $affaireRepository->findAllOngoingByDate($userId, $collaborateursChoisi);
                }else{
                    $affaires = $affaireRepository->findAllOngoingByDate($userId, $collaborateurs);
                }
                $filter = 'date';
            }
        }
        

        
        // Check if the user is logged in
            if (!$user instanceof User) {
                throw new LogicException('Aucun utilisateur connecté');
            }
        

        // // Retrieve affaires related to the user
        // $affaires = $affaireRepository->findAllByUser($user->getId());

    
        $affairesAll = $affaireRepository->findAll();

        if (isset($firstDate)) {
            $dateDebut = clone $firstDate; // Use clone to create a new instance
        } else {
            $dateDebut = new DateTime('2024-01-01');
        }
        
        if (isset($lastDate)) {
            $dateFin = clone $lastDate; // Use clone to create a new instance
        } else {
            $dateFin = new DateTime('2024-12-30');
        }

        $annee1 = $firstDate->format('Y');
        $tableauDates = array();

        while ($dateDebut <= $dateFin) {
            $annee = $dateDebut->format('Y');
            $jourSemaine = $dateDebut->format('l');
            $dateFormatee = $dateDebut->format('m/d');
            $numeroSemaine = $dateDebut->format('W');

            $tableauDates[] = array(
                'annee' => $annee,
                'jourSemaine' => $jourSemaine,
                'dateFormatee' => $dateFormatee,
                'numeroSemaine' => $numeroSemaine
            );

            $dateDebut->modify('+1 day'); // Passage à la prochaine journée
        }

        
        
        $joursFeries = [];

        // Jour de l'an
        $joursFeries[] = new \DateTime("$annee1-01-01");

        // Fête du Travail
        $joursFeries[] = new \DateTime("$annee1-05-01");

        // Victoire des Alliés
        $joursFeries[] = new \DateTime("$annee1-05-08");

        // Fête Nationale
        $joursFeries[] = new \DateTime("$annee1-07-14");

        // Assomption
        $joursFeries[] = new \DateTime("$annee1-08-15");

        // Toussaint
        $joursFeries[] = new \DateTime("$annee1-11-01");

        // Armistice
        $joursFeries[] = new \DateTime("$annee1-11-11");

        // Noël
        $joursFeries[] = new \DateTime("$annee1-12-25");

        // Récupérer le lundi de Pâques
        $joursFeries[] = $this->getPaques($annee1)->modify('+1 day');

        // Récupérer l'Ascension
        $joursFeries[] = $this->getPaques($annee1)->modify('+39 days');

        // Récupérer le lundi de Pentecôte
        $joursFeries[] = $this->getPaques($annee1)->modify('+50 days');

        // Formater les dates pour l'affichage
        $formattedDates = [];
        foreach ($joursFeries as $jourFerie) {
            $formattedDates[] = $jourFerie->format('Y-m-d');
        }
        $annee2 = $annee1 + 1 ;
        // Jour de l'an
        $joursFeries[] = new \DateTime("$annee2-01-01");

        // Fête du Travail
        $joursFeries[] = new \DateTime("$annee2-05-01");

        // Victoire des Alliés
        $joursFeries[] = new \DateTime("$annee2-05-08");

        // Fête Nationale
        $joursFeries[] = new \DateTime("$annee2-07-14");

        // Assomption
        $joursFeries[] = new \DateTime("$annee2-08-15");

        // Toussaint
        $joursFeries[] = new \DateTime("$annee2-11-01");

        // Armistice
        $joursFeries[] = new \DateTime("$annee2-11-11");

        // Noël
        $joursFeries[] = new \DateTime("$annee2-12-25");

        // Récupérer le lundi de Pâques
        $joursFeries[] = $this->getPaques($annee2)->modify('+1 day');

        // Récupérer l'Ascension
        $joursFeries[] = $this->getPaques($annee2)->modify('+39 days');

        // Récupérer le lundi de Pentecôte
        $joursFeries[] = $this->getPaques($annee2)->modify('+50 days');

        // Formater les dates pour l'affichage
        $formattedDates = [];
        foreach ($joursFeries as $jourFerie) {
            $formattedDates[] = $jourFerie->format('m/d');
        }

        $weekend = ['Saturday', 'Sunday'];



        return $this->render('affaire/index.html.twig', [
            'affaires' => $affaires,
            'affairesAll' => $affairesAll,
            'tableauDates' => $tableauDates,
            'ferie' => $joursFeries,
            'weekend' => $weekend,
            'collaborateurs' => isset($collaborateursChoisi) ? $collaborateursChoisi : $collaborateurs,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'role' => $role,
            'filter' => isset($filter) ? $filter : null,
            'feriesFormatee' => $formattedDates,
        ]);
    }

    #[Route('/update/fini/{id}', name: 'update_fini')]
    public function updateFini(int $id, EntityManagerInterface $entityManager): Response
    {
        $affaire = $entityManager->getRepository(Affaire::class)->find($id);
    
        if (!$affaire) {
            throw $this->createNotFoundException('Affaire not found with id ' . $id);
        }
    
        $affaire->setFini(true);
    
        $entityManager->flush();
    
        return $this->redirectToRoute('affaire_index');
    }

    #[Route('/new', name: 'new_affaire')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $affaire = new Affaire();

        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
            $isSuperAdmin = true;
        }else{
            $isSuperAdmin = false;
        }

        $form = $this->createForm(AffaireType::class, $affaire, [
            'userId' => $userId,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajoutez ici toute logique de traitement nécessaire pour la création d'une affaire
            $em->persist($affaire);
            $em->flush();

            return $this->redirectToRoute('affaire_index');
        }

        return $this->render('affaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'affaire_edit')]
    public function edit(Request $request, Affaire $affaire, Security $security, PersistenceManagerRegistry $doctrine): Response
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $security->getUser();        
        $userId = $user->getId();

        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
            $isSuperAdmin = true;
        }else{
            $isSuperAdmin = false;
        }
        
        // Récupérer le collaborateur en charge de l'affaire
        $collaborateur = $affaire->getCollaborateur()[0];

        // Récupérer le représentant associé au collaborateur
        $representant = $collaborateur->getRepresentant();

        // Vérifier si l'utilisateur est autorisé à modifier cette affaire
        if ($user->getId() !== $representant->getId() && $isSuperAdmin == false) {
            // Rediriger l'utilisateur ou afficher un message d'erreur
            // Vous pouvez personnaliser cela en fonction de vos besoins
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette affaire.');
        }



        $form = $this->createForm(AffaireType::class, $affaire, [
            'userId' => $userId,
            'isSuperAdmin' => $isSuperAdmin,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('affaire_index');
        }

        return $this->render('affaire/edit.html.twig', [
            'affaire' => $affaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_affaire')]
    public function delete(Request $request, int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $affaire = $entityManager->getRepository(Affaire::class)->find($id);

        if (!$affaire) {
            throw $this->createNotFoundException('Affaire not found with id ' . $id);
        }

        if ($this->isCsrfTokenValid('delete'.$affaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($affaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('affaire_index');
    }

    private function getPaques(int $annee): \DateTime
    {
        $a = $annee % 19;
        $b = (int)($annee / 100);
        $c = $annee % 100;
        $d = (int)($b / 4);
        $e = $b % 4;
        $f = (int)(($b + 8) / 25);
        $g = (int)(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = (int)($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = (int)(($a + 11 * $h + 22 * $l) / 451);
        $month = (int)(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return new \DateTime("$annee-$month-$day");
    }
}
