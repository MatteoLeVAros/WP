<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UtilisateurRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function register(array $data): Utilisateur
    {
        if (empty($data['email']) || empty($data['password'])) {
            throw new BadRequestHttpException('Email et mot de passe obligatoires.');
        }

        $existing = $this->userRepository->findOneBy(['email' => $data['email']]);

        if ($existing) {
            throw new BadRequestHttpException('Email déjà utilisé.');
        }

        $user = new Utilisateur();
        $user->setEmail($data['email']);
        $user->setNom($data['nom'] ?? '');
        $user->setPrenom($data['prenom'] ?? '');
        $user->setFonction($data['fonction'] ?? null);
        $user->setTelephone($data['telephone'] ?? null);
        $user->setDisponibilite(true);

        // hash password
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );

        $user->setPasswordHash($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function updateProfile(Utilisateur $user, array $data): Utilisateur
    {
        if (isset($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (isset($data['prenom'])) {
            $user->setPrenom($data['prenom']);
        }

        if (isset($data['fonction'])) {
            $user->setFonction($data['fonction']);
        }

        if (isset($data['telephone'])) {
            $user->setTelephone($data['telephone']);
        }

        if (isset($data['disponibilite'])) {
            $user->setDisponibilite($data['disponibilite']);
        }

        // changement de mot de passe (optionnel)
        if (!empty($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setPasswordHash($hashedPassword);
        }

        $this->em->flush();

        return $user;
    }
}