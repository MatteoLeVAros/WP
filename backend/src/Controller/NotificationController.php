<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/notifications')]
class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $notifications = $this->notificationService->findForUser($user);

        return $this->json($notifications, 200, [], ['groups' => 'notification:list']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $notification = $this->notificationService->findOneForUser($id, $user);

        return $this->json($notification, 200, [], ['groups' => 'notification:detail']);
    }

    #[Route('/{id}/read', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function markAsRead(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $notification = $this->notificationService->markAsRead($id, $user);

        return $this->json($notification, 200, [], ['groups' => 'notification:detail']);
    }
}