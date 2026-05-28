<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Repository\UtilisateurRepository;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService
    ) {
    }

    
    #[Route('/users', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $users = $utilisateurRepository->findBy([], ['nom' => 'ASC']);

        return $this->json($users, 200, [], ['groups' => ['user:list']]);
    }


    #[Route('/me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        return $this->json($user, 200, [], ['groups' => 'user:detail']);
    }

    #[Route('/me', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new BadRequestHttpException('Body JSON invalide');
        }

        $updatedUser = $this->userService->updateProfile($user, $data);

        return $this->json($updatedUser, 200, [], ['groups' => 'user:detail']);
    }
}