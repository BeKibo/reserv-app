<?php

namespace App\Entity;

use App\Repository\CritErgoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CritErgoRepository::class)]
class CritErgo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\Length(min: 2, max: 80, minMessage: 'Le nom contient au minimum {{ min }} caractères et au maximum {{ max }} caractères')]
    #[Assert\Regex(pattern: '/^[A-Za-z0-9-]+$/', message: "Seuls les lettres, chiffres et tirets sont autorisés.")]
    private ?string $nom = null;

    /**
     * @var Collection<int, Salle>
     */
    #[ORM\ManyToMany(targetEntity: Salle::class, mappedBy: 'critergo')]
    private Collection $salles;

    public function __construct()
    {
        $this->salles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getSalles(): Collection
    {
        return $this->salles;
    }

    public function addSalle(Salle $salle): static
    {
        if (!$this->salles->contains($salle)) {
            $this->salles->add($salle);
            $salle->addCritergo($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): static
    {
        if ($this->salles->removeElement($salle)) {
            $salle->removeCritergo($this);
        }

        return $this;
    }
}
