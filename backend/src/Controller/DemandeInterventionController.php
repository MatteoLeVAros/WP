<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\DemandeInterventionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route('/api/demandes-intervention')]
class DemandeInterventionController extends AbstractController
{
    public function __construct(
        private DemandeInterventionService $demandeService
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

        $demandes = $this->demandeService->findForUser($user);

        return $this->json($demandes, 200, [], ['groups' => 'demande:list']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id): JsonResponse
    {
        $demande = $this->demandeService->findOne($id);

        return $this->json($demande, 200, [], ['groups' => 'demande:detail']);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            throw new BadRequestHttpException('Body JSON invalide.');
        }

        $demande = $this->demandeService->create($data, $user);

        return $this->json($demande, 201, [], ['groups' => 'demande:detail']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            throw new BadRequestHttpException('Body JSON invalide.');
        }

        $demande = $this->demandeService->update($id, $data, $user);

        return $this->json($demande, 200, [], ['groups' => 'demande:detail']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $this->demandeService->delete($id, $user);

        return $this->json(['message' => 'Demande supprimée']);
    }
}