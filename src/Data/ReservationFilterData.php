<?php

namespace App\Data;

use DateTimeInterface;

class ReservationFilterData
{
    public ?string $nom = null;
    public ?int $capaciteMin = null;
    public ?string $lieu = null;
    public ?DateTimeInterface $dateDebut = null;
    public ?DateTimeInterface $dateFin = null;
    public array $critergos = [];
    public array $equipements = [];
}
