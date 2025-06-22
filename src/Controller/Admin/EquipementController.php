<?php

namespace App\Controller\Admin;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/equipement', name: 'admin_equipement_')]
class EquipementController extends AdminController
{
    private $equipementRepository;
    private $entityManager;

    public function __construct(
        NotificationService $notificationService,
        EquipementRepository $equipementRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($notificationService);
        $this->equipementRepository = $equipementRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $equipements = $this->equipementRepository->findAll();

        return $this->renderAdmin('admin/equipement/index.html.twig', [
            'equipements' => $equipements,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($equipement);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'équipement a été créé avec succès.');

            return $this->redirectToRoute('admin_equipement_index');
        }

        return $this->renderAdmin('admin/equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Equipement $equipement): Response
    {
        return $this->renderAdmin('admin/equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipement $equipement): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'équipement a été mis à jour avec succès.');

            return $this->redirectToRoute('admin_equipement_index');
        }

        return $this->renderAdmin('admin/equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Equipement $equipement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipement->getId(), $request->request->get('_token'))) {
            // Check if the equipment is associated with any rooms
            if (!$equipement->getSalles()->isEmpty()) {
                $this->addFlash('danger', 'Impossible de supprimer cet équipement car il est associé à des salles.');
                return $this->redirectToRoute('admin_equipement_index');
            }

            $this->entityManager->remove($equipement);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'équipement a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_equipement_index');
    }
}
