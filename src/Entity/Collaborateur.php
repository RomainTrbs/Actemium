<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CollaborateurRepository;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: CollaborateurRepository::class)]
class Collaborateur implements UserInterface
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collaborateur')]
    private User $representant ;

    #[ORM\ManyToOne(targetEntity: Poste::class, inversedBy: 'collaborateur')]
    private Poste $poste ;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private array $roles = [];

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

    public function getRepresentant(): ?User
    {
        return $this->representant;
    }

    public function setRepresentant(?User $representant): static
    {
        $this->representant = $representant;

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

        /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }
        /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
