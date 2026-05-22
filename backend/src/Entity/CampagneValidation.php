<?php

namespace App\Entity;

use App\Repository\CampagneValidationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CampagneValidationRepository::class)]
class CampagneValidation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['campagne:list', 'campagne:detail', 'commentaire:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['campagne:list', 'campagne:detail', 'commentaire:detail'])]
    private ?string $referenceCampagne = null;

    #[ORM\Column(length: 100)]
    #[Groups(['campagne:list', 'campagne:detail', 'commentaire:detail'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['campagne:detail'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['campagne:list', 'campagne:detail', 'commentaire:detail'])]
    private ?string $statut = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['campagne:list', 'campagne:detail', 'commentaire:detail'])]
    private ?string $priorite = null;

    #[ORM\Column]
    #[Groups(['campagne:list', 'campagne:detail'])]
    private ?\DateTime $dateDebutPrevue = null;

    #[ORM\Column]
    #[Groups(['campagne:list', 'campagne:detail'])]
    private ?\DateTime $dateFinPrevue = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['campagne:detail'])]
    private ?\DateTime $dateDebutReelle = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['campagne:detail'])]
    private ?\DateTime $dateFinReelle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['campagne:detail'])]
    private ?string $commentaireGlobal = null;

    #[ORM\Column]
    #[Groups(['campagne:list', 'campagne:detail'])]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['campagne:detail'])]
    private ?\DateTime $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'campagnesResponsables')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['campagne:detail'])]
    private ?Utilisateur $responsable = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferenceCampagne(): ?string
    {
        return $this->referenceCampagne;
    }

    public function setReferenceCampagne(string $referenceCampagne): static
    {
        $this->referenceCampagne = $referenceCampagne;

        return $this;
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

    public function getDateDebutPrevue(): ?\DateTime
    {
        return $this->dateDebutPrevue;
    }

    public function setDateDebutPrevue(\DateTime $dateDebutPrevue): static
    {
        $this->dateDebutPrevue = $dateDebutPrevue;

        return $this;
    }

    public function getDateFinPrevue(): ?\DateTime
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(\DateTime $dateFinPrevue): static
    {
        $this->dateFinPrevue = $dateFinPrevue;

        return $this;
    }

    public function getDateDebutReelle(): ?\DateTime
    {
        return $this->dateDebutReelle;
    }

    public function setDateDebutReelle(?\DateTime $dateDebutReelle): static
    {
        $this->dateDebutReelle = $dateDebutReelle;

        return $this;
    }

    public function getDateFinReelle(): ?\DateTime
    {
        return $this->dateFinReelle;
    }

    public function setDateFinReelle(?\DateTime $dateFinReelle): static
    {
        $this->dateFinReelle = $dateFinReelle;

        return $this;
    }

    public function getCommentaireGlobal(): ?string
    {
        return $this->commentaireGlobal;
    }

    public function setCommentaireGlobal(?string $commentaireGlobal): static
    {
        $this->commentaireGlobal = $commentaireGlobal;

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

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getResponsable(): ?Utilisateur
    {
        return $this->responsable;
    }

    public function setResponsable(?Utilisateur $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }
}