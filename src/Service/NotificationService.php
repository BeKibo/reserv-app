<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use DateTimeImmutable;

class NotificationService
{
    private $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Get all notifications for the admin panel
     *
     * @return array
     */
    public function getAdminNotifications(): array
    {
        $notifications = [];

        // Get unvalidated reservations that are approaching their start date (5 days)
        $upcomingReservations = $this->getUpcomingUnvalidatedReservations();

        foreach ($upcomingReservations as $reservation) {
            $notifications[] = [
                'title' => 'Réservation en attente',
                'message' => sprintf(
                    'La réservation de %s pour la salle %s du %s est en attente de validation.',
                    $reservation->getUsers()->getNom(),
                    $reservation->getSalles()->getNom(),
                    $reservation->getDateDebut()->format('d/m/Y H:i')
                ),
                'date' => new \DateTime(),
                'link' => '/admin/reservation/' . $reservation->getId() . '/edit'
            ];
        }

        return $notifications;
    }

    /**
     * Get the count of pending reservations
     *
     * @return int
     */
    public function getPendingReservationsCount(): int
    {
        return count($this->reservationRepository->findBy(['validation' => false]));
    }

    /**
     * Get unvalidated reservations that are approaching their start date (5 days)
     *
     * @return array
     */
    private function getUpcomingUnvalidatedReservations(): array
    {
        $now = new DateTimeImmutable();
        $fiveDaysLater = $now->modify('+5 days');

        $reservations = $this->reservationRepository->createQueryBuilder('r')
            ->where('r.validation = :validation')
            ->andWhere('r.dateDebut <= :fiveDaysLater')
            ->andWhere('r.dateDebut >= :now')
            ->setParameter('validation', false)
            ->setParameter('fiveDaysLater', $fiveDaysLater)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        return $reservations;
    }
}
