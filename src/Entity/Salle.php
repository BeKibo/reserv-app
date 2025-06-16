<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column]
    private ?int $capacite = null;

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

    public function __construct()
    {
        $this->Equipement = new ArrayCollection();
        $this->critergo = new ArrayCollection();
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
}
