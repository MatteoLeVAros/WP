<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\CommentaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route('/api/commentaires')]
class CommentaireController extends AbstractController
{
    public function __construct(
        private CommentaireService $commentaireService
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): JsonResponse
    {
        $tacheId = $request->query->get('tache');
        $campagneId = $request->query->get('campagne');

        if ($tacheId && $campagneId) {
            throw new BadRequestHttpException(
                'Vous devez filtrer soit par tâche, soit par campagne, pas les deux.'
            );
        }

        if (!$tacheId && !$campagneId) {
            throw new BadRequestHttpException(
                'Vous devez préciser un paramètre "tache" ou "campagne".'
            );
        }

        if ($tacheId) {
            $commentaires = $this->commentaireService->findByTache((int) $tacheId);

            return $this->json($commentaires, 200, [], ['groups' => 'commentaire:list']);
        }

        $commentaires = $this->commentaireService->findByCampagne((int) $campagneId);

        return $this->json($commentaires, 200, [], ['groups' => 'commentaire:list']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id): JsonResponse
    {
        $commentaire = $this->commentaireService->findOne($id);

        return $this->json($commentaire, 200, [], ['groups' => 'commentaire:detail']);
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
            throw new BadRequestHttpException('Le body JSON est invalide.');
        }

        $commentaire = $this->commentaireService->create($data, $user);

        return $this->json($commentaire, 201, [], ['groups' => 'commentaire:detail']);
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
            throw new BadRequestHttpException('Le body JSON est invalide.');
        }

        $commentaire = $this->commentaireService->update($id, $data, $user);

        return $this->json($commentaire, 200, [], ['groups' => 'commentaire:detail']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        $this->commentaireService->delete($id, $user);

        return $this->json([
            'message' => 'Commentaire supprimé'
        ]);
    }
}