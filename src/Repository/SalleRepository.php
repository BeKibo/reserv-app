<?php

namespace App\Repository;

use App\Entity\Salle;
use App\Data\ReservationFilterData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Salle>
 */
class SalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salle::class);
    }

    public function findWithFilter(ReservationFilterData $data): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.critergo', 'c')
            ->leftJoin('s.equipement', 'e')
            ->addSelect('c', 'e');

        if ($data->nom) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $data->nom . '%');
        }

        if ($data->lieu) {
            $qb->andWhere('s.lieu = :lieu')
                ->setParameter('lieu', $data->lieu);
        }

        if ($data->capaciteMin) {
            $qb->andWhere('s.capacite >= :capaciteMin')
                ->setParameter('capaciteMin', $data->capaciteMin);
        }

        if (!empty($data->critergos)) {
            $qb->andWhere('c.id IN (:critergos)')
                ->setParameter('critergos', $data->critergos);
        }

        if (!empty($data->equipement)) {
            $qb->andWhere('e.id IN (:equipement)')
                ->setParameter('equipement', $data->equipements);
        }

        if ($data->dateDebut && $data->dateFin) {
            $qb->andWhere('s.id NOT IN (
                SELECT IDENTITY(r.salle) FROM App\Entity\Reservation r
                WHERE (
                    (:debut BETWEEN r.startDate AND r.endDate)
                    OR (:fin BETWEEN r.startDate AND r.endDate)
                    OR (r.startDate BETWEEN :debut AND :fin)
                )
            )')
                ->setParameter('debut', $data->dateDebut)
                ->setParameter('fin', $data->dateFin);
        }

        return $qb->getQuery()->getResult();
    }
}
