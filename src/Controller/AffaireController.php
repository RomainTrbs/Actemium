<?php

namespace App\Controller;

use App\Entity\Affaire;
use App\Controller\AffaireController;
use App\Repository\AffaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class AffaireController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(AffaireRepository $affaireRepository): Response
    {
        $affaires = $affaireRepository->findAll();

        return $this->render('affaire/index.html.twig', [
            'affaires' => $affaires,
        ]);
    }

    #[Route('/update/fini/{id}', name: 'update_fini')]
    public function updateFini(Request $request, Affaire $affaire ,PersistenceManagerRegistry $doctrine): Response
    {
        $affaire->setFini(true);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($affaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_index');
    }
}
