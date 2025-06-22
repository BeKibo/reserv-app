<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Salle;
use App\Repository\ReservationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    private $entityManager;
    private $reservationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Create a new reservation
     *
     * @param Reservation $reservation
     * @return bool
     */
    public function createReservation(Reservation $reservation): bool
    {
        // Check if the room is available for the requested time period
        if (!$this->isRoomAvailable($reservation->getSalles(), $reservation->getDateDebut(), $reservation->getDateFin())) {
            return false;
        }

        // Set validation to false by default (pending)
        $reservation->setValidation(false);

        // Save the reservation
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Validate a reservation
     *
     * @param Reservation $reservation
     * @return bool
     */
    public function validateReservation(Reservation $reservation): bool
    {
        // Check if the room is still available for the requested time period
        if (!$this->isRoomAvailable($reservation->getSalles(), $reservation->getDateDebut(), $reservation->getDateFin(), $reservation->getId())) {
            return false;
        }

        // Set validation to true
        $reservation->setValidation(true);

        // Save the reservation
        $this->entityManager->flush();

        return true;
    }

    /**
     * Check if a room is available for a given time period
     *
     * @param Salle $salle
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @param int|null $excludeReservationId
     * @return bool
     */
    public function isRoomAvailable(Salle $salle, DateTimeImmutable $start, DateTimeImmutable $end, ?int $excludeReservationId = null): bool
    {
        $qb = $this->reservationRepository->createQueryBuilder('r')
            ->where('r.salles = :salle')
            ->andWhere('r.validation = :validation')
            ->andWhere('r.dateDebut < :end')
            ->andWhere('r.dateFin > :start')
            ->setParameter('salle', $salle)
            ->setParameter('validation', true)
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if ($excludeReservationId) {
            $qb->andWhere('r.id != :id')
               ->setParameter('id', $excludeReservationId);
        }

        $conflictingReservations = $qb->getQuery()->getResult();

        return count($conflictingReservations) === 0;
    }

    /**
     * Get statistics for the dashboard
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $now = new DateTimeImmutable();
        $startOfMonth = new DateTimeImmutable('first day of this month midnight');
        $endOfMonth = new DateTimeImmutable('last day of this month 23:59:59');

        // Total reservations
        $totalReservations = count($this->reservationRepository->findAll());

        // Pending reservations
        $pendingReservations = count($this->reservationRepository->findBy(['validation' => false]));

        // Confirmed reservations
        $confirmedReservations = count($this->reservationRepository->findBy(['validation' => true]));

        // Reservations this month
        $reservationsThisMonth = count($this->reservationRepository->createQueryBuilder('r')
            ->where('r.dateDebut >= :startOfMonth')
            ->andWhere('r.dateDebut <= :endOfMonth')
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('endOfMonth', $endOfMonth)
            ->getQuery()
            ->getResult());

        // Upcoming reservations (next 7 days)
        $sevenDaysLater = $now->modify('+7 days');
        $upcomingReservations = count($this->reservationRepository->createQueryBuilder('r')
            ->where('r.dateDebut >= :now')
            ->andWhere('r.dateDebut <= :sevenDaysLater')
            ->setParameter('now', $now)
            ->setParameter('sevenDaysLater', $sevenDaysLater)
            ->getQuery()
            ->getResult());

        return [
            'total' => $totalReservations,
            'pending' => $pendingReservations,
            'confirmed' => $confirmedReservations,
            'thisMonth' => $reservationsThisMonth,
            'upcoming' => $upcomingReservations,
        ];
    }
}
