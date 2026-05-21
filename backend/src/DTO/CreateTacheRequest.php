<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTacheRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $titre = null;

    public ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['a_faire', 'en_cours', 'terminee', 'bloquee'])]
    public ?string $statut = null;

    #[Assert\Choice(choices: ['basse', 'moyenne', 'haute', 'critique'])]
    public ?string $priorite = null;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dateDebut = null;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dateEcheance = null;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dateFin = null;

    public ?int $campagneId = null;
    public ?int $assigneAId = null;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public ?int $createurId = null;
}