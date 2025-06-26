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

    /**
     * Récupère les salles filtrées selon les critères (nom, lieu, capacité, équipements, critErgo, dates).
     */
    public function findWithFilter(ReservationFilterData $data): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.critergo', 'c')
            ->leftJoin('s.equipement', 'e')
            ->addSelect('c', 'e');

        //  Filtre par nom
        if ($data->nom) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $data->nom . '%');
        }

        //  Filtre par capacité minimum
        if ($data->capaciteMin) {
            $qb->andWhere('s.capacite >= :capaciteMin')
                ->setParameter('capaciteMin', $data->capaciteMin);
        }

        //  Filtre par lieu (entité Salle attendue dans le filtre)
        if (!empty($data->ville)) {
            $qb->andWhere('s.lieu LIKE :ville')
                ->setParameter('ville', '%' . $data->ville . '%');
        }


        //  Filtre par critères ergonomiques
        if (!empty($data->critergos)) {
            $qb->andWhere('c IN (:criteres)')
                ->setParameter('criteres', $data->critergos);
        }

        //  Filtre par équipements
        if (!empty($data->equipements)) {
            $qb->andWhere('e IN (:equipements)')
                ->setParameter('equipements', $data->equipements);
        }

        //  Filtre par disponibilité (exclure les salles déjà réservées et validées à ces dates)
        if ($data->dateDebut && $data->dateFin) {
            $qb->andWhere('s.id NOT IN (
                SELECT s_inner.id
                FROM App\Entity\Reservation r
                JOIN r.salles s_inner
                WHERE (r.validation = true OR r.validation = false)
                AND r.dateFin > :debut
                AND r.dateDebut < :fin
            )')
                ->setParameter('debut', $data->dateDebut)
                ->setParameter('fin', $data->dateFin);
        }

        return $qb->getQuery()->getResult();
    }
}
