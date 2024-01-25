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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class AffaireController extends AbstractController
{
    #[Route('/', name: 'affaire_index')]
    public function index(AffaireRepository $affaireRepository, UserRepository $userRepository, CollaborateurRepository $collaborateurRepository, Security $security, Request $request): Response
    {
    
        $user = $this->getUser();
        $user = $user->getCollaborateur();
        $user = $user->getRepresentant();
        $affaires = $affaireRepository->findAllByUser($user->getId());

        $filter = 'collaborateur';

        $session = $request->getSession();
        $session->start();

    // Retrieve stored dates from the session or use default values
    $firstDate = $session->get('firstDate', new \DateTime('01-01-2024'));
    $lastDate = $session->get('lastDate', new \DateTime('31-12-2024'));

    // Create the form with the stored values
    $form = $this->createFormBuilder(['firstDate' => $firstDate, 'lastDate' => $lastDate])
        ->add('firstDate', DateType::class)
        ->add('lastDate', DateType::class)
        ->add('validate', SubmitType::class, [
            'label' => 'Validate',
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
                case 'collaborateur':
                    $affaires = $affaireRepository->findAllOngoingByCollaborateur($user->getId());
                    $filter = 'collaborateur';
                    break;
                case 'client':
                    $affaires = $affaireRepository->findAllOngoingByClient($user->getId());
                    $filter = 'client';
                    break;
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

        $ferie = ['01/01', '04/01','05/01', '05/08', '05/09', '05/19', '05/20', '07/14' ,'08/15', '11/01', '11/11', '12/25'];

        $weekend = ['Saturday', 'Sunday'];



        return $this->render('affaire/index.html.twig', [
            'affaires' => $affaires,
            'affairesAll' => $affairesAll,
            'tableauDates' => $tableauDates,
            'ferie' => $ferie,
            'weekend' => $weekend,
            'filter' => $filter,
            'form' => $form->createView(),
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

        $form = $this->createForm(AffaireType::class, $affaire, [
            'userId' => $userId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajoutez ici toute logique de traitement nécessaire pour la création d'une affaire
            $em->persist($affaire);
            $em->flush();

            return $this->redirectToRoute('affaire_index', ['id' => $affaire->getId()]);
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
        
        // Récupérer le collaborateur en charge de l'affaire
        $collaborateur = $affaire->getCollaborateur();

        // Récupérer le représentant associé au collaborateur
        $representant = $collaborateur->getRepresentant();

        // Vérifier si l'utilisateur est autorisé à modifier cette affaire
        if ($user->getId() !== $representant->getId()) {
            // Rediriger l'utilisateur ou afficher un message d'erreur
            // Vous pouvez personnaliser cela en fonction de vos besoins
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette affaire.');
        }

        $form = $this->createForm(AffaireType::class, $affaire, [
            'userId' => $userId,
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
}
