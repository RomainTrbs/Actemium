<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Form\PosteType;
use App\Repository\PosteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CollaborateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/poste')]
class PosteController extends AbstractController
{
    #[Route('/', name: 'poste_index')]
    public function index(PosteRepository $posteRepository): Response
    {
        $postes = $posteRepository->findAll();

        return $this->render('poste/index.html.twig', [
            'controller_name' => 'PosteController',
            'postes' => $postes
        ]);
    }

    #[Route('/new', name: 'poste_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $poste = new Poste();

        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajoutez ici toute logique de traitement nécessaire pour la création d'une affaire
            $em->persist($poste);
            $em->flush();

            return $this->redirectToRoute('poste_index');
        }

        return $this->render('poste/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit_poste')]
    public function edit(Request $request, Poste $poste, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('show_poste', [
                'id' => $poste->getId(),
            ]);
        }

        return $this->render('poste/edit.html.twig', [
            'poste' => $poste,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show_poste')]
    public function show(Poste $poste, CollaborateurRepository $collaborateurRepository): Response
    {
        $collaborateurs = $collaborateurRepository->FindAllByPoste($poste);

        return $this->render('poste/show.html.twig', [
            'collaborateurs' => $collaborateurs,
            'poste' => $poste,
        ]);
    }

    #[Route('/{id}', name: 'delete_poste')]
    public function delete(Request $request, Poste $poste): Response
    {
        if ($this->isCsrfTokenValid('delete'.$poste->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($poste);
            $entityManager->flush();
        }

        return $this->redirectToRoute('poste_index');
    }
}
