<?php

namespace App\Controller;

use App\Service\TacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/taches')]
class TacheController extends AbstractController
{
    public function __construct(
        private TacheService $tacheService
    ) {}

    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof \App\Entity\Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $filters = [
            'statut' => $request->query->get('statut'),
            'priorite' => $request->query->get('priorite'),
            'assigneA' => $request->query->get('assigneA'),
            'campagne' => $request->query->get('campagne'),
            'search' => $request->query->get('search'),
        ];

        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);

        // Si ce n'est pas un admin, on force l'affichage uniquement de ses tâches
        if (!$isAdmin) {
            $filters['assigneA'] = $user->getId();
        }

        $taches = $this->tacheService->search($filters);

        return $this->json($taches, 200, [], ['groups' => ['tache:list']]);
    }

    // ✅ DETAIL
    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id): JsonResponse
    {
        $tache = $this->tacheService->findOne($id);

        return $this->json($tache, 200, [], ['groups' => 'tache:detail']);
    }

    // ✅ UPDATE
    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $tache = $this->tacheService->update($id, $data);

        return $this->json($tache, 200, [], ['groups' => 'tache:detail']);
    }

    // ✅ DELETE
    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(int $id): JsonResponse
    {
        $this->tacheService->delete($id);

        return $this->json([
            'message' => 'Tâche supprimée'
        ]);
    }
}
