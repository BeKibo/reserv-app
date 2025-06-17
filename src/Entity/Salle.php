<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
    #[Assert\Regex(pattern: '/^[A-Za-z0-9-]+$/',message: "Seuls les lettres, chiffres et tirets sont autorisés.")]
    private ?string $nom = null;

    #[ORM\Column(length: 125, nullable: true)]
    #[Assert\Length(min: 2, max: 125, minMessage: 'Le lieu contient au minimum {{ min }} caractères et au maximum {{ max }} caractères')]
    #[Assert\Regex(pattern: '/^[A-Za-z0-9-]+$/', message: "Seuls les lettres, chiffres et tirets sont autorisés.")]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\Length(min:2 , max:3, minMessage: 'La capacité minimum est de {{ min }} et au maximum de {{ max }} ')]
    #[Assert\Type(type: 'integer', message: 'Doit être un nombre entier.')]
    private ?int $capacite = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: '{{ max }} caractères maximum')]
    #[Assert\Regex(pattern: '/\.(jpg|jpeg|png|webp)$/')]
    private ?string $image = 'default.jpg';


    #[ORM\Column(nullable: false)]
    private ?bool $statut = null;


    /**
     * @var Collection<int, Equipement>
     */
    #[ORM\ManyToMany(targetEntity: Equipement::class, inversedBy: 'salles')]
    private Collection $Equipement;

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


    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateStatut(): void
    {
        // Exemple : la salle est disponible s’il n’y a aucune réservation
        $this->statut = $this->reservation->isEmpty();
    }

    public function __construct()
    {
        $this->Equipement = new ArrayCollection();
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

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipement(): Collection
    {
        return $this->Equipement;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->Equipement->contains($equipement)) {
            $this->Equipement->add($equipement);
        }

        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        $this->Equipement->removeElement($equipement);

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

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

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
            // set the owning side to null (unless already changed)
            if ($reservation->getSalles() === $this) {
                $reservation->setSalles(null);
            }
        }

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
}
