<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification:list', 'notification:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['notification:list', 'notification:detail'])]
    private ?string $typeNotification = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification:list', 'notification:detail'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:detail'])]
    private ?string $message = null;

    #[ORM\Column(length: 20)]
    #[Groups(['notification:list', 'notification:detail'])]
    private ?string $canal = null;

    #[ORM\Column]
    #[Groups(['notification:list', 'notification:detail'])]
    private ?bool $estLue = null;

    #[ORM\Column]
    #[Groups(['notification:list', 'notification:detail'])]
    private ?\DateTime $dateEnvoi = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['notification:detail'])]
    private ?\DateTime $dateLecture = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:detail'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne]
    #[Groups(['notification:detail'])]
    private ?Tache $tache = null;

    #[ORM\ManyToOne]
    #[Groups(['notification:detail'])]
    private ?CampagneValidation $campagne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeNotification(): ?string
    {
        return $this->typeNotification;
    }

    public function setTypeNotification(string $typeNotification): static
    {
        $this->typeNotification = $typeNotification;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCanal(): ?string
    {
        return $this->canal;
    }

    public function setCanal(string $canal): static
    {
        $this->canal = $canal;

        return $this;
    }

    public function isEstLue(): ?bool
    {
        return $this->estLue;
    }

    public function setEstLue(bool $estLue): static
    {
        $this->estLue = $estLue;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTime
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTime $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getDateLecture(): ?\DateTime
    {
        return $this->dateLecture;
    }

    public function setDateLecture(?\DateTime $dateLecture): static
    {
        $this->dateLecture = $dateLecture;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getTache(): ?Tache
    {
        return $this->tache;
    }

    public function setTache(?Tache $tache): static
    {
        $this->tache = $tache;

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
