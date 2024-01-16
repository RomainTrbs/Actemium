<?php

namespace App\Entity;

use App\Repository\CollaborateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollaborateurRepository::class)]
class Collaborateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?float $hr_jour = null;

    #[ORM\Column(nullable: true)]
    private ?int $hr_semaine = null;

    #[ORM\Column(nullable: true)]
    private ?int $jour_semaine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $representant = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getHrJour(): ?float
    {
        return $this->hr_jour;
    }

    public function setHrJour(?float $hr_jour): static
    {
        $this->hr_jour = $hr_jour;

        return $this;
    }

    public function getHrSemaine(): ?int
    {
        return $this->hr_semaine;
    }

    public function setHrSemaine(?int $hr_semaine): static
    {
        $this->hr_semaine = $hr_semaine;

        return $this;
    }

    public function getJourSemaine(): ?int
    {
        return $this->jour_semaine;
    }

    public function setJourSemaine(?int $jour_semaine): static
    {
        $this->jour_semaine = $jour_semaine;

        return $this;
    }

    public function getRepresentant(): ?string
    {
        return $this->representant;
    }

    public function setRepresentant(?string $representant): static
    {
        $this->representant = $representant;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): static
    {
        $this->service = $service;

        return $this;
    }
}
