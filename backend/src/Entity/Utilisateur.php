<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tache:detail', 'campagne:detail', 'notification:detail', 'commentaire:detail', 'user:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['tache:detail', 'campagne:detail', 'notification:detail', 'commentaire:detail', 'user:detail'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $passwordHash = null;

    #[ORM\Column(length: 100)]
    #[Groups(['tache:detail', 'campagne:detail', 'notification:detail', 'commentaire:detail', 'user:detail'])]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Groups(['tache:detail', 'campagne:detail', 'notification:detail', 'commentaire:detail', 'user:detail'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['tache:detail', 'campagne:detail', 'notification:detail', 'commentaire:detail', 'user:detail'])]
    private ?string $fonction = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tache:detail', 'campagne:detail', 'notification:detail', 'commentaire:detail', 'user:detail'])]
    private ?bool $disponibilite = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['tache:detail', 'campagne:detail', 'notification:detail', 'commentaire:detail', 'user:detail'])]
    private ?string $telephone = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, CampagneValidation>
     */
    #[ORM\OneToMany(targetEntity: CampagneValidation::class, mappedBy: 'responsable')]
    private Collection $campagnesResponsables;

    /**
     * @var Collection<int, DemandeIntervention>
     */
    #[ORM\OneToMany(targetEntity: DemandeIntervention::class, mappedBy: 'demandeur')]
    private Collection $demandeInterventions;

    public function __construct()
    {
        $this->campagnesResponsables = new ArrayCollection();
        $this->demandeInterventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): static
    {
        $this->passwordHash = $passwordHash;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    #[Override]
    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return Collection<int, CampagneValidation>
     */
    public function getcampagnesResponsables(): Collection
    {
        return $this->campagnesResponsables;
    }

    public function addcampagnesResponsable(CampagneValidation $campagnesResponsable): static
    {
        if (!$this->campagnesResponsables->contains($campagnesResponsable)) {
            $this->campagnesResponsables->add($campagnesResponsable);
            $campagnesResponsable->setResponsable($this);
        }

        return $this;
    }

    public function removecampagnesResponsable(CampagneValidation $campagnesResponsable): static
    {
        if ($this->campagnesResponsables->removeElement($campagnesResponsable)) {
            if ($campagnesResponsable->getResponsable() === $this) {
                $campagnesResponsable->setResponsable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeIntervention>
     */
    public function getDemandeInterventions(): Collection
    {
        return $this->demandeInterventions;
    }

    public function addDemandeIntervention(DemandeIntervention $demandeIntervention): static
    {
        if (!$this->demandeInterventions->contains($demandeIntervention)) {
            $this->demandeInterventions->add($demandeIntervention);
            $demandeIntervention->setDemandeur($this);
        }

        return $this;
    }

    public function removeDemandeIntervention(DemandeIntervention $demandeIntervention): static
    {
        if ($this->demandeInterventions->removeElement($demandeIntervention)) {
            // set the owning side to null (unless already changed)
            if ($demandeIntervention->getDemandeur() === $this) {
                $demandeIntervention->setDemandeur(null);
            }
        }

        return $this;
    }
}