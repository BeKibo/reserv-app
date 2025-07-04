<?php

namespace App\Entity;

use App\Entity\Reservation;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SalleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(min: 2, max: 80, minMessage: 'Le nom contient au minimum {{ min }} caractères et au maximum {{ max }} caractères')]
    #[Assert\Regex(pattern: '/^[A-Za-z0-9-]+$/', message: "Seuls les lettres, chiffres et tirets sont autorisés.")]
    private ?string $nom = null;

    #[ORM\Column(length: 125, nullable: true)]
    #[Assert\Length(min: 2, max: 125, minMessage: 'Le lieu contient au minimum {{ min }} caractères et au maximum {{ max }} caractères')]
    #[Assert\Regex(pattern: '/^[A-Za-z0-9-]+$/', message: "Seuls les lettres, chiffres et tirets sont autorisés.")]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\Length(min: 2, max: 3, minMessage: 'La capacité minimum est de {{ min }} et au maximum de {{ max }} ')]
    #[Assert\Type(type: 'integer', message: 'Doit être un nombre entier.')]
    private ?int $capacite = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: '{{ max }} caractères maximum')]
    #[Assert\Regex(pattern: '/\.(jpg|jpeg|png|webp)$/')]
    private ?string $image = 'default.jpg';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 20, max: 300, minMessage: 'Le lieu contient au minimum {{ min }} caractères et au maximum {{ max }} caractères')]
    private ?string $description = null;

    /**
     * @var Collection<int, Equipement>
     */
    #[ORM\ManyToMany(targetEntity: Equipement::class, inversedBy: 'salles')]
    private Collection $equipement;

    /**
     * @var Collection<int, CritErgo>
     */
    #[ORM\ManyToMany(targetEntity: CritErgo::class, inversedBy: 'salles')]
    private Collection $critergo;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'salles')]
    private Collection $reservation;

    public function __construct()
    {
        $this->equipement = new ArrayCollection();
        $this->critergo = new ArrayCollection();
        $this->reservation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipement(): Collection
    {
        return $this->equipement;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipement->contains($equipement)) {
            $this->equipement->add($equipement);
        }

        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        $this->equipement->removeElement($equipement);
        return $this;
    }

    /**
     * @return Collection<int, CritErgo>
     */
    public function getCritergo(): Collection
    {
        return $this->critergo;
    }

    public function addCritergo(CritErgo $critergo): static
    {
        if (!$this->critergo->contains($critergo)) {
            $this->critergo->add($critergo);
        }

        return $this;
    }

    public function removeCritergo(CritErgo $critergo): static
    {
        $this->critergo->removeElement($critergo);
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
            $reservation->setSalles($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservation->removeElement($reservation)) {
            if ($reservation->getSalles() === $this) {
                $reservation->setSalles(null);
            }
        }

        return $this;
    }

    public function isReservedBetween(\DateTimeImmutable $start, \DateTimeImmutable $end): bool
    {
        foreach ($this->reservation as $res) {
            if ($res->isValidation() && $start < $res->getDateFin() && $end > $res->getDateDebut()) {
                return true;
            }
        }
        return false;
    }

    public function __toString(): string
    {
        return $this->nom ?? 'Salle #' . $this->id;
    }
}
