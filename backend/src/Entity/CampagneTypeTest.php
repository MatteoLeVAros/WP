<?php

namespace App\Entity;

use App\Repository\CampagneTypeTestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampagneTypeTestRepository::class)]
class CampagneTypeTest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CampagneValidation $campagne = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeTest $typeTest = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeTest(): ?TypeTest
    {
        return $this->typeTest;
    }

    public function setTypeTest(?TypeTest $typeTest): static
    {
        $this->typeTest = $typeTest;

        return $this;
    }
}
