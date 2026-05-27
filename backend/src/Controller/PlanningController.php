<?php

namespace App\Controller;

use App\Service\PlanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/planning')]
class PlanningController extends AbstractController
{
    public function __construct(
        private PlanningService $planningService
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'type' => $request->query->get('type'),
            'statut' => $request->query->get('statut'),
            'priorite' => $request->query->get('priorite'),
            'responsable' => $request->query->get('responsable'),
            'assigneA' => $request->query->get('assigneA'),
            'search' => $request->query->get('search'),
            'from' => $request->query->get('from'),
            'to' => $request->query->get('to'),
        ];

        $items = $this->planningService->getPlanning($filters);

        return $this->json($items);
    }
}