<?php

namespace App\Data;

use App\Entity\Salle;
use DateTimeInterface;

class ReservationFilterData
{
    public ?string $nom = null;
    public ?int $capaciteMin = null;
    public ?Salle $lieu = null;
    public ?DateTimeInterface $dateDebut = null;
    public ?DateTimeInterface $dateFin = null;
    public array $critergos = [];
}
