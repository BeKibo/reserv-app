<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Vérifie si une salle est déjà réservée (validée) à ces dates.
     */
    public function isSalleReservedBetween(Salle $salle, \DateTimeInterface $start, \DateTimeInterface $end): bool
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.salles = :salle')
            ->andWhere('r.validation = true')
            ->andWhere('r.dateFin > :start')
            ->andWhere('r.dateDebut < :end')
            ->setParameter('salle', $salle)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
