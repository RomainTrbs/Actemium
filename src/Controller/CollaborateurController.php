<?php

namespace App\Controller;

use DateTime;
use App\Form\AffaireType;
use App\Entity\Collaborateur;
use App\Form\CollaborateurType;
use App\Repository\StatusRepository;
use App\Repository\AffaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CollaborateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/collaborateur')]
class CollaborateurController extends AbstractController
{
    #[Route('/', name: 'collaborateur_index')]
    public function index(CollaborateurRepository $collaborateurRepository, StatusRepository $statusRepository): Response
    {
        $status = $statusRepository->find(2); 
        $collaborateurs = $collaborateurRepository->findAllByStatus($status);

        return $this->render('collaborateur/index.html.twig', [
            'controller_name' => 'CollaborateurController',
            'collaborateurs' => $collaborateurs
        ]);
    }

    #[Route('/new', name: 'new_collaborateur')]
    public function new(Request $request, EntityManagerInterface $em, StatusRepository $statusRepository): Response
    {
        $collaborateur = new Collaborateur();

        $form = $this->createForm(CollaborateurType::class, $collaborateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajoutez ici toute logique de traitement nécessaire pour la création d'une affaire

            $status = $statusRepository->find(2); 
            $collaborateur->setStatus($status);

            $em->persist($collaborateur);
            $em->flush();

            return $this->redirectToRoute('collaborateur_index');
        }

        return $this->render('collaborateur/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit_collaborateur')]
    public function edit(Request $request, Collaborateur $collaborateur, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CollaborateurType::class, $collaborateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('show_collaborateur', [
                'id' => $collaborateur->getId(),
            ]);
        }

        return $this->render('collaborateur/edit.html.twig', [
            'collaborateur' => $collaborateur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show_collaborateur')]
    public function show(Collaborateur $collaborateur, AffaireRepository $affaireRepository): Response
    {
        $affaires = $collaborateur->getAffaires();

        $dateDebut = new DateTime('2024-01-01'); // Date de début
        $dateFin = new DateTime('2024-02-29'); // Date de fin

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


        return $this->render('collaborateur/show.html.twig', [
            'collaborateur' => $collaborateur,
            'affaires' => $affaires,
            'tableauDates' => $tableauDates,
            'ferie' => $ferie,
            'weekend' => $weekend,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_collaborateur')]
    public function delete(Request $request, Collaborateur $collaborateur, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collaborateur->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($collaborateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('collaborateur_index');
    }

}
