<?php

namespace App\Controller\Admin;

use App\Entity\CritErgo;
use App\Form\CritErgoType;
use App\Repository\CritErgoRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/critergo', name: 'admin_critergo_')]
class CritErgoController extends AdminController
{
    private $critErgoRepository;
    private $entityManager;

    public function __construct(
        NotificationService $notificationService,
        CritErgoRepository $critErgoRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($notificationService);
        $this->critErgoRepository = $critErgoRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $critergos = $this->critErgoRepository->findAll();

        return $this->renderAdmin('admin/critergo/index.html.twig', [
            'critergos' => $critergos,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $critergo = new CritErgo();
        $form = $this->createForm(CritErgoType::class, $critergo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($critergo);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le critère ergonomique a été créé avec succès.');

            return $this->redirectToRoute('admin_critergo_index');
        }

        return $this->renderAdmin('admin/critergo/new.html.twig', [
            'critergo' => $critergo,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(CritErgo $critergo): Response
    {
        return $this->renderAdmin('admin/critergo/show.html.twig', [
            'critergo' => $critergo,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CritErgo $critergo): Response
    {
        $form = $this->createForm(CritErgoType::class, $critergo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Le critère ergonomique a été mis à jour avec succès.');

            return $this->redirectToRoute('admin_critergo_index');
        }

        return $this->renderAdmin('admin/critergo/edit.html.twig', [
            'critergo' => $critergo,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, CritErgo $critergo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$critergo->getId(), $request->request->get('_token'))) {
            // Check if the critergo is associated with any rooms
            if (!$critergo->getSalles()->isEmpty()) {
                $this->addFlash('danger', 'Impossible de supprimer ce critère ergonomique car il est associé à des salles.');
                return $this->redirectToRoute('admin_critergo_index');
            }

            $this->entityManager->remove($critergo);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le critère ergonomique a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_critergo_index');
    }
}
