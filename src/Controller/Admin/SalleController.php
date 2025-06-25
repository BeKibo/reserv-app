<?php

namespace App\Controller\Admin;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/salle', name: 'admin_salle_')]
class SalleController extends AdminController
{
    private $salleRepository;
    private $entityManager;

    public function __construct(
        NotificationService $notificationService,
        SalleRepository $salleRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($notificationService);
        $this->salleRepository = $salleRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $salles = $this->salleRepository->findAll();

        return $this->renderAdmin('admin/salle/index.html.twig', [
            'salles' => $salles,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($salle);
            $this->entityManager->flush();
            $this->addFlash('success', 'La salle a été créée avec succès.');

            return $this->redirectToRoute('admin_salle_index');
        }

        return $this->renderAdmin('admin/salle/new.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Salle $salle): Response
    {
        return $this->renderAdmin('admin/salle/show.html.twig', [
            'salle' => $salle,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salle $salle): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'La salle a été mise à jour avec succès.');

            return $this->redirectToRoute('admin_salle_index');
        }

        return $this->renderAdmin('admin/salle/edit.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Salle $salle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salle->getId(), $request->request->get('_token'))) {
            // Check if the room has reservations
            if (!$salle->getReservation()->isEmpty()) {
                $this->addFlash('danger', 'Impossible de supprimer cette salle car elle a des réservations associées.');
                return $this->redirectToRoute('admin_salle_index');
            }

            $this->entityManager->remove($salle);
            $this->entityManager->flush();
            $this->addFlash('success', 'La salle a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_salle_index');
    }
}
