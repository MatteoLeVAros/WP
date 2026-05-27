<?php

namespace App\Entity;

use App\Repository\DemandeInterventionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeInterventionRepository::class)]
class DemandeIntervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeIntervention = null;

    #[ORM\Column(length: 255)]
    private ?string $projetNumeroMoyenValidation = null;

    #[ORM\Column(length: 100)]
    private ?string $systeme = null;

    #[ORM\Column(length: 255)]
    private ?string $emplacementMoyenBadge = null;

    #[ORM\Column(length: 50)]
    private ?string $dureeIntervention = null;

    #[ORM\Column]
    private ?\DateTime $dateDemarrageSouhaitee = null;

    #[ORM\Column]
    private ?\DateTime $dateLimiteLivraison = null;

    #[ORM\Column]
    private ?int $nombreIntervenants = null;

    #[ORM\Column(length: 50)]
    private ?string $besoinConducteurPermisC = null;

    #[ORM\Column(length: 255)]
    private ?string $lienStockagePvalLogs = null;

    #[ORM\Column(length: 100)]
    private ?string $statusInstrumentation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $lienTemplateChecklist = null;

    #[ORM\Column(length: 255)]
    private ?string $versionSwValider = null;

    #[ORM\Column(length: 255)]
    private ?string $canEnregister = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $statutDemande = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column]
    private ?\DateTime $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'demandeInterventions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $demandeur = null;

    #[ORM\ManyToOne(inversedBy: 'demandeInterventions')]
    private ?CampagneValidation $campagne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeIntervention(): ?string
    {
        return $this->typeIntervention;
    }

    public function setTypeIntervention(string $typeIntervention): static
    {
        $this->typeIntervention = $typeIntervention;

        return $this;
    }

    public function getProjetNumeroMoyenValidation(): ?string
    {
        return $this->projetNumeroMoyenValidation;
    }

    public function setProjetNumeroMoyenValidation(string $projetNumeroMoyenValidation): static
    {
        $this->projetNumeroMoyenValidation = $projetNumeroMoyenValidation;

        return $this;
    }

    public function getSysteme(): ?string
    {
        return $this->systeme;
    }

    public function setSysteme(string $systeme): static
    {
        $this->systeme = $systeme;

        return $this;
    }

    public function getEmplacementMoyenBadge(): ?string
    {
        return $this->emplacementMoyenBadge;
    }

    public function setEmplacementMoyenBadge(string $emplacementMoyenBadge): static
    {
        $this->emplacementMoyenBadge = $emplacementMoyenBadge;

        return $this;
    }

    public function getDureeIntervention(): ?string
    {
        return $this->dureeIntervention;
    }

    public function setDureeIntervention(string $dureeIntervention): static
    {
        $this->dureeIntervention = $dureeIntervention;

        return $this;
    }

    public function getDateDemarrageSouhaitee(): ?\DateTime
    {
        return $this->dateDemarrageSouhaitee;
    }

    public function setDateDemarrageSouhaitee(\DateTime $dateDemarrageSouhaitee): static
    {
        $this->dateDemarrageSouhaitee = $dateDemarrageSouhaitee;

        return $this;
    }

    public function getDateLimiteLivraison(): ?\DateTime
    {
        return $this->dateLimiteLivraison;
    }

    public function setDateLimiteLivraison(\DateTime $dateLimiteLivraison): static
    {
        $this->dateLimiteLivraison = $dateLimiteLivraison;

        return $this;
    }

    public function getNombreIntervenants(): ?int
    {
        return $this->nombreIntervenants;
    }

    public function setNombreIntervenants(int $nombreIntervenants): static
    {
        $this->nombreIntervenants = $nombreIntervenants;

        return $this;
    }

    public function getBesoinConducteurPermisC(): ?string
    {
        return $this->besoinConducteurPermisC;
    }

    public function setBesoinConducteurPermisC(string $besoinConducteurPermisC): static
    {
        $this->besoinConducteurPermisC = $besoinConducteurPermisC;

        return $this;
    }

    public function getLienStockagePvalLogs(): ?string
    {
        return $this->lienStockagePvalLogs;
    }

    public function setLienStockagePvalLogs(string $lienStockagePvalLogs): static
    {
        $this->lienStockagePvalLogs = $lienStockagePvalLogs;

        return $this;
    }

    public function getStatusInstrumentation(): ?string
    {
        return $this->statusInstrumentation;
    }

    public function setStatusInstrumentation(string $statusInstrumentation): static
    {
        $this->statusInstrumentation = $statusInstrumentation;

        return $this;
    }

    public function getLienTemplateChecklist(): ?string
    {
        return $this->lienTemplateChecklist;
    }

    public function setLienTemplateChecklist(string $lienTemplateChecklist): static
    {
        $this->lienTemplateChecklist = $lienTemplateChecklist;

        return $this;
    }

    public function getVersionSwValider(): ?string
    {
        return $this->versionSwValider;
    }

    public function setVersionSwValider(string $versionSwValider): static
    {
        $this->versionSwValider = $versionSwValider;

        return $this;
    }

    public function getCanEnregister(): ?string
    {
        return $this->canEnregister;
    }

    public function setCanEnregister(string $canEnregister): static
    {
        $this->canEnregister = $canEnregister;

        return $this;
    }

    public function getStatutDemande(): ?string
    {
        return $this->statutDemande;
    }

    public function setStatutDemande(?string $statutDemande): static
    {
        $this->statutDemande = $statutDemande;

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

    public function setDateModification(\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getDemandeur(): ?Utilisateur
    {
        return $this->demandeur;
    }

    public function setDemandeur(?Utilisateur $demandeur): static
    {
        $this->demandeur = $demandeur;

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
}
