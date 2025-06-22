<?php

namespace App\Controller\Admin;

use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use App\Service\ReservationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class DashboardController extends AdminController
{
    private $reservationService;
    private $reservationRepository;
    private $salleRepository;
    private $userRepository;

    public function __construct(
        NotificationService $notificationService,
        ReservationService $reservationService,
        ReservationRepository $reservationRepository,
        SalleRepository $salleRepository,
        UserRepository $userRepository
    ) {
        parent::__construct($notificationService);
        $this->reservationService = $reservationService;
        $this->reservationRepository = $reservationRepository;
        $this->salleRepository = $salleRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {
        // Get statistics
        $reservationStats = $this->reservationService->getStatistics();
        $roomCount = count($this->salleRepository->findAll());
        $userCount = count($this->userRepository->findAll());

        // Get recent reservations
        $recentReservations = $this->reservationRepository->findBy([], ['dateDebut' => 'DESC'], 5);

        return $this->renderAdmin('admin/dashboard/index.html.twig', [
            'stats' => [
                'reservations' => $reservationStats,
                'rooms' => $roomCount,
                'users' => $userCount,
            ],
            'recent_reservations' => $recentReservations,
        ]);
    }
}
