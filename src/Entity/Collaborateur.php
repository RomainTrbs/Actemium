<?php

namespace App\Entity;

use App\Repository\CollaborateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollaborateurRepository::class)]
class Collaborateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?float $hr_jour = null;

    #[ORM\Column(nullable: true)]
    private ?float $hr_semaine = null;

    #[ORM\Column(nullable: true)]
    private ?int $jour_semaine = null;

    #[ORM\ManyToOne]
    private ?Poste $poste = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne]
    private ?User $representant = null;

    #[ORM\ManyToMany(targetEntity: Affaire::class, mappedBy: 'collaborateur')]
    private Collection $affaires;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couleur = null;

    public function __construct()
    {
        $this->affaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getHrJour(): ?float
    {
        return $this->hr_jour;
    }

    public function setHrJour(float $hr_jour): static
    {
        $this->hr_jour = $hr_jour;

        return $this;
    }

    public function getHrSemaine(): ?float
    {
        return $this->hr_semaine;
    }

    public function setHrSemaine(float $hr_semaine): static
    {
        $this->hr_semaine = $hr_semaine;

        return $this;
    }

    public function getJourSemaine(): ?int
    {
        return $this->jour_semaine;
    }

    public function setJourSemaine(int $jour_semaine): static
    {
        $this->jour_semaine = $jour_semaine;

        return $this;
    }

    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    public function setPoste(?Poste $poste): static
    {
        $this->poste = $poste;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRepresentant(): ?User
    {
        return $this->representant;
    }

    public function setRepresentant(?User $representant): static
    {
        $this->representant = $representant;

        return $this;
    }

    /**
     * @return Collection<int, Affaire>
     */
    public function getAffaires(): Collection
    {
        return $this->affaires;
    }

    public function addAffaire(Affaire $affaire): static
    {
        if (!$this->affaires->contains($affaire)) {
            $this->affaires->add($affaire);
            $affaire->addCollaborateur($this);
        }

        return $this;
    }

    public function removeAffaire(Affaire $affaire): static
    {
        if ($this->affaires->removeElement($affaire)) {
            $affaire->removeCollaborateur($this);
        }

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }
}
