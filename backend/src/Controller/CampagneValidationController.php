<?php

namespace App\Controller;

use App\Entity\CampagneValidation;
use App\Repository\CampagneValidationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/campagnes')]
class CampagneValidationController extends AbstractController
{
    // ✅ LISTE DES CAMPAGNES
    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $Request, CampagneValidationRepository $campagneValidationRepository): JsonResponse 
    {
        $filters = [
            'statut' => $Request->query->get('statut'),
            'priorite' => $Request->query->get('priorite'),
            'responsable' => $Request->query->get('responsable'),
            'search' => $Request->query->get('search'),
        ];
        $campagnes = $campagneValidationRepository->search($filters);

        return $this->json($campagnes, 200, [], ['groups' => ['campagne:list']]);
    }

    // ✅ DETAIL CAMPAGNE
    #[Route('/{id}', name: 'campagne_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(CampagneValidation $campagne): JsonResponse
    {
        return $this->json($campagne, 200, [], ['groups' => ['campagne:detail']]);
    }

    // ✅ CREATION CAMPAGNE
    #[Route('', name: 'campagne_create', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function create(
    Request $request,
    EntityManagerInterface $em,
    UtilisateurRepository $userRepo,
    \App\Repository\DemandeInterventionRepository $demandeRepo
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!is_array($data)) {
        return $this->json(['message' => 'Body JSON invalide'], 400);
    }

    $required = ['referenceCampagne', 'titre', 'statut', 'dateDebutPrevue', 'dateFinPrevue'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            return $this->json(['message' => sprintf('Le champ "%s" est obligatoire.', $field)], 400);
        }
    }

    $campagne = new CampagneValidation();

    $campagne->setReferenceCampagne($data['referenceCampagne']);
    $campagne->setTitre($data['titre']);
    $campagne->setDescription($data['description'] ?? null);
    $campagne->setStatut($data['statut']);
    $campagne->setPriorite($data['priorite'] ?? null);
    $campagne->setDateDebutPrevue(new \DateTime($data['dateDebutPrevue']));
    $campagne->setDateFinPrevue(new \DateTime($data['dateFinPrevue']));
    $campagne->setCommentaireGlobal($data['commentaireGlobal'] ?? null);
    $campagne->setDateCreation(new \DateTime());

    // Responsable / technicien assigné
    if (!empty($data['responsableId'])) {
        $responsable = $userRepo->find((int) $data['responsableId']);

        if (!$responsable) {
            return $this->json(['message' => 'Responsable introuvable'], 404);
        }

        $campagne->setResponsable($responsable);
    } else {
        // fallback : utilisateur connecté
        $campagne->setResponsable($this->getUser());
    }

    // Liaison avec une demande d’intervention
    if (!empty($data['demandeInterventionId'])) {
        $demande = $demandeRepo->find((int) $data['demandeInterventionId']);

        if (!$demande) {
            return $this->json(['message' => 'Demande intervention introuvable'], 404);
        }

        $campagne->addDemandeIntervention($demande);

        // Optionnel mais très utile métier
        $demande->setStatutDemande('planifiee');
        $demande->setDateModification(new \DateTime());
    }

    $em->persist($campagne);
    $em->flush();

    return $this->json($campagne, 201, [], ['groups' => ['campagne:detail']]);
}

    // ✅ UPDATE CAMPAGNE
    #[Route('/{id}', name: 'campagne_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(
        CampagneValidation $campagne,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['titre'])) {
            $campagne->setTitre($data['titre']);
        }

        if (isset($data['description'])) {
            $campagne->setDescription($data['description']);
        }

        if (isset($data['statut'])) {
            $campagne->setStatut($data['statut']);
        }

        if (isset($data['priorite'])) {
            $campagne->setPriorite($data['priorite']);
        }

        if (isset($data['dateDebutPrevue'])) {
            $campagne->setDateDebutPrevue(new \DateTime($data['dateDebutPrevue']));
        }

        if (isset($data['dateFinPrevue'])) {
            $campagne->setDateFinPrevue(new \DateTime($data['dateFinPrevue']));
        }

        $campagne->setDateModification(new \DateTime());

        $em->flush();

        return $this->json($campagne, 200, [], ['groups' => ['campagne:detail']]);
    }

    // ✅ DELETE CAMPAGNE
    #[Route('/{id}', name: 'campagne_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        CampagneValidation $campagne,
        EntityManagerInterface $em
    ): JsonResponse {
        $em->remove($campagne);
        $em->flush();

        return $this->json([
            'message' => 'Campagne supprimée'
        ], 200);
    }
}