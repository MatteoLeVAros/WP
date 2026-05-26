<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/users', methods: ['GET'])]
    public function listUsers(UtilisateurRepository $repo): JsonResponse
    {
        $users = $repo->findAll();

        return $this->json($users, 200, [], ['groups' => 'user:detail']);
    }
}