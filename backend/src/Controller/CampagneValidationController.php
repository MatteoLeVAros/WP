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
    #[Route('', name: 'campagne_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(CampagneValidationRepository $repo): JsonResponse
    {
        $campagnes = $repo->findAll();

        return $this->json($campagnes, 200, [], ['groups' => 'campagne:list']);
    }

    // ✅ DETAIL CAMPAGNE
    #[Route('/{id}', name: 'campagne_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(CampagneValidation $campagne): JsonResponse
    {
        return $this->json($campagne, 200, [], ['groups' => 'campagne:detail']);
    }

    // ✅ CREATION CAMPAGNE
    #[Route('', name: 'campagne_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $userRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $campagne = new CampagneValidation();

        // Remplissage
        $campagne->setReferenceCampagne($data['referenceCampagne']);
        $campagne->setTitre($data['titre']);
        $campagne->setDescription($data['description'] ?? null);
        $campagne->setStatut($data['statut']);
        $campagne->setPriorite($data['priorite'] ?? null);
        $campagne->setDateDebutPrevue(new \DateTime($data['dateDebutPrevue']));
        $campagne->setDateFinPrevue(new \DateTime($data['dateFinPrevue']));
        $campagne->setCommentaireGlobal($data['commentaireGlobal'] ?? null);

        // ✅ Responsable = utilisateur connecté
        $campagne->setResponsable($this->getUser());

        $campagne->setDateCreation(new \DateTime());

        $em->persist($campagne);
        $em->flush();

        return $this->json($campagne, 201, [], ['groups' => 'campagne:detail']);
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

        return $this->json($campagne, 200, [], ['groups' => 'campagne:detail']);
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