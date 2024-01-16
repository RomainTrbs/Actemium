<?php

namespace App\Controller;

use DateTime;
use App\Entity\Affaire;
use App\Controller\AffaireController;
use App\Repository\AffaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\AffaireType;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class AffaireController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(AffaireRepository $affaireRepository): Response
    {
        $affaires = $affaireRepository->findAll();

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



        return $this->render('affaire/index.html.twig', [
            'affaires' => $affaires,
            'tableauDates' => $tableauDates,
            'ferie' => $ferie,
            'weekend' => $weekend,
        ]);
    }

    #[Route('/update/fini/{id}', name: 'update_fini')]
    public function updateFini(Request $request): Response
    {
        $affaire->setFini(true);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($affaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_index');
    }

    #[Route('/new', name: 'new_affaire')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $affaire = new Affaire();

        $form = $this->createForm(AffaireType::class, $affaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajoutez ici toute logique de traitement nécessaire pour la création d'une affaire
            $em->persist($affaire);
            $em->flush();

            return $this->redirectToRoute('app_index', ['id' => $affaire->getId()]);
        }

        return $this->render('affaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
