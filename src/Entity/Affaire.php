<?php

namespace App\Entity;

use App\Repository\AffaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffaireRepository::class)]
#[ORM\Table(name: "affaires")]
class Affaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $fini = null;

    #[ORM\Column(nullable: true)]
    private ?int $num_affaire = null;

    #[ORM\Column(length: 255)]
    private ?string $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $collaborateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designation = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbre_heure = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(nullable: true)]
    private ?int $heure_passe = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFini(): ?bool
    {
        return $this->fini;
    }

    public function setFini(?bool $fini): static
    {
        $this->fini = $fini;

        return $this;
    }

    public function getNumAffaire(): ?int
    {
        return $this->num_affaire;
    }

    public function setNumAffaire(?int $num_affaire): static
    {
        $this->num_affaire = $num_affaire;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCollaborateur(): ?string
    {
        return $this->collaborateur;
    }

    public function setCollaborateur(string $collaborateur): static
    {
        $this->collaborateur = $collaborateur;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getNbreHeure(): ?int
    {
        return $this->nbre_heure;
    }

    public function setNbreHeure(?int $nbre_heure): static
    {
        $this->nbre_heure = $nbre_heure;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getHeurePasse(): ?int
    {
        return $this->heure_passe;
    }

    public function setHeurePasse(?int $heure_passe): static
    {
        $this->heure_passe = $heure_passe;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }
}
