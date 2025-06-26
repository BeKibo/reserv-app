<?php

namespace App\Data;

use App\Entity\CritErgo;

class ReservationFilterData
{
    public ?string $nom = null;
    public ?int $capaciteMin = null;
    public ?string $ville = null;
    public ?\DateTimeInterface $dateDebut = null;
    public ?\DateTimeInterface $dateFin = null;

    /** @var CritErgo[]|null */
    public ?array $critergos = [];
}
