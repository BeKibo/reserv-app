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
            $qb->andWhere('c IN (:criteres)')
                ->setParameter('criteres', $data->critergos);
        }

        if (!empty($data->equipements)) {
            $qb->andWhere('e IN (:equipements)')
                ->setParameter('equipements', $data->equipements);
        }

        if ($data->dateDebut && $data->dateFin) {
            $qb->andWhere('s.id NOT IN (
                SELECT salle_inner.id FROM App\Entity\Reservation r
                JOIN r.salles salle_inner
                WHERE (
                    (:debut BETWEEN r.dateDebut AND r.dateFin)
                    OR (:fin BETWEEN r.dateDebut AND r.dateFin)
                    OR (r.dateDebut BETWEEN :debut AND :fin)
                )
            )')
            ->setParameter('debut', $data->dateDebut)
            ->setParameter('fin', $data->dateFin);
        }

        return $qb->getQuery()->getResult();
    }
}
