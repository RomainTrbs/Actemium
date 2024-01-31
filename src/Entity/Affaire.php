<?php

namespace App\Entity;

use App\Repository\AffaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffaireRepository::class)]
class Affaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $num_affaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designation = null;

    #[ORM\Column]
    private ?float $nbre_heure = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(nullable: true)]
    private ?float $heure_passe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbre_jour_fractionnement = null;

    #[ORM\Column(nullable: true)]
    private ?int $pourcent_reserve = null;

    #[ORM\ManyToMany(targetEntity: Collaborateur::class, inversedBy: 'affaires')]
    private Collection $collaborateur;

    public function __construct()
    {
        $this->collaborateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumAffaire(): ?string
    {
        return $this->num_affaire;
    }

    public function setNumAffaire(?string $num_affaire): static
    {
        $this->num_affaire = $num_affaire;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): static
    {
        $this->client = $client;

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

    public function getNbreHeure(): ?float
    {
        return $this->nbre_heure;
    }

    public function setNbreHeure(float $nbre_heure): static
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

    public function getHeurePasse(): ?float
    {
        return $this->heure_passe;
    }

    public function setHeurePasse(float $heure_passe): static
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

    public function getNbreJourFractionnement(): ?int
    {
        return $this->nbre_jour_fractionnement;
    }

    public function setNbreJourFractionnement(?int $nbre_jour_fractionnement): static
    {
        $this->nbre_jour_fractionnement = $nbre_jour_fractionnement;

        return $this;
    }

    public function getPourcentReserve(): ?int
    {
        return $this->pourcent_reserve;
    }

    public function setPourcentReserve(?int $pourcent_reserve): static
    {
        $this->pourcent_reserve = $pourcent_reserve;

        return $this;
    }

    /**
     * @return Collection<int, Collaborateur>
     */
    public function getCollaborateur(): Collection
    {
        return $this->collaborateur;
    }

    public function addCollaborateur(Collaborateur $collaborateur): static
    {
        if (!$this->collaborateur->contains($collaborateur)) {
            $this->collaborateur->add($collaborateur);
        }

        return $this;
    }

    public function removeCollaborateur(Collaborateur $collaborateur): static
    {
        $this->collaborateur->removeElement($collaborateur);

        return $this;
    }
}
