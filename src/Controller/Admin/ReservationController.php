<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Service\NotificationService;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservation', name: 'admin_reservation_')]
class ReservationController extends AdminController
{
    private $reservationRepository;
    private $reservationService;
    private $entityManager;

    public function __construct(
        NotificationService $notificationService,
        ReservationRepository $reservationRepository,
        ReservationService $reservationService,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($notificationService);
        $this->reservationRepository = $reservationRepository;
        $this->reservationService = $reservationService;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Get filter parameters
        $status = $request->query->get('status');
        $criteria = [];

        if ($status === 'pending') {
            $criteria['validation'] = false;
        } elseif ($status === 'confirmed') {
            $criteria['validation'] = true;
        }

        $reservations = $this->reservationRepository->findBy($criteria, ['dateDebut' => 'DESC']);

        return $this->renderAdmin('admin/reservation/index.html.twig', [
            'reservations' => $reservations,
            'current_filter' => $status,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->reservationService->createReservation($reservation)) {
                $this->addFlash('success', 'La réservation a été créée avec succès.');
                return $this->redirectToRoute('admin_reservation_index');
            } else {
                $this->addFlash('danger', 'La salle est déjà réservée pour cette période.');
            }
        }

        return $this->renderAdmin('admin/reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->renderAdmin('admin/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if validation status has changed
            $wasValidated = $reservation->isValidation();
            $isNowValidated = $form->get('validation')->getData();

            if (!$wasValidated && $isNowValidated) {
                // Reservation is being validated
                if ($this->reservationService->validateReservation($reservation)) {
                    $this->addFlash('success', 'La réservation a été validée avec succès.');
                } else {
                    $this->addFlash('danger', 'La salle est déjà réservée pour cette période.');
                    return $this->renderAdmin('admin/reservation/edit.html.twig', [
                        'reservation' => $reservation,
                        'form' => $form->createView(),
                    ]);
                }
            } else {
                // Just updating other fields
                $this->entityManager->flush();
                $this->addFlash('success', 'La réservation a été mise à jour avec succès.');
            }

            return $this->redirectToRoute('admin_reservation_index');
        }

        return $this->renderAdmin('admin/reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($reservation);
            $this->entityManager->flush();
            $this->addFlash('success', 'La réservation a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_reservation_index');
    }

    #[Route('/{id}/validate', name: 'validate', methods: ['GET'])]
    public function validate(Reservation $reservation): Response
    {
        if (!$reservation->isValidation()) {
            if ($this->reservationService->validateReservation($reservation)) {
                $this->addFlash('success', 'La réservation a été validée avec succès.');
            } else {
                $this->addFlash('danger', 'La salle est déjà réservée pour cette période.');
            }
        }

        return $this->redirectToRoute('admin_reservation_index');
    }
}
