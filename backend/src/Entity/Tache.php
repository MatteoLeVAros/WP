<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tache:list', 'tache:detail', 'commentaire:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tache:list', 'tache:detail', 'commentaire:detail'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['tache:detail'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['tache:list', 'tache:detail', 'commentaire:detail'])]
    private ?string $statut = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['tache:list', 'tache:detail', 'commentaire:detail'])]
    private ?string $priorite = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tache:detail'])]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column]
    #[Groups(['tache:list', 'tache:detail'])]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tache:detail'])]
    private ?\DateTime $dateEcheance = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tache:detail'])]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tache:detail'])]
    private ?\DateTime $dateModification = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['tache:list', 'tache:detail'])]
    private ?CampagneValidation $campagne = null;

    #[ORM\ManyToOne]
    #[Groups(['tache:detail'])]
    private ?Utilisateur $assigneA = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tache:detail'])]
    private ?Utilisateur $createur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(?string $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateEcheance(): ?\DateTime
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(?\DateTime $dateEcheance): static
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getCampagne(): ?CampagneValidation
    {
        return $this->campagne;
    }

    public function setCampagne(?CampagneValidation $campagne): static
    {
        $this->campagne = $campagne;

        return $this;
    }

    public function getAssigneA(): ?Utilisateur
    {
        return $this->assigneA;
    }

    public function setAssigneA(?Utilisateur $assigneA): static
    {
        $this->assigneA = $assigneA;

        return $this;
    }

    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateur $createur): static
    {
        $this->createur = $createur;

        return $this;
    }
}