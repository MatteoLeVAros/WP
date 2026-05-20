<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Repository\TacheRepository;
use App\Repository\CampagneValidationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/taches')]
class TacheController extends AbstractController
{
    // ✅ LISTE DES TACHES
    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(TacheRepository $repo): JsonResponse
    {
        $taches = $repo->findAll();

        return $this->json($taches, 200, [], ['groups' => 'tache:list']);
    }

    // ✅ DETAIL TACHE
    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Tache $tache): JsonResponse
    {
        return $this->json($tache, 200, [], ['groups' => 'tache:detail']);
    }

    // ✅ CREATION TACHE
    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        CampagneValidationRepository $campagneRepo,
        UtilisateurRepository $userRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $tache = new Tache();

        $tache->setTitre($data['titre']);
        $tache->setDescription($data['description'] ?? null);
        $tache->setStatut($data['statut']);
        $tache->setPriorite($data['priorite'] ?? null);

        $tache->setDateDebut(
            isset($data['dateDebut']) ? new \DateTime($data['dateDebut']) : null
        );
        $tache->setDateEcheance(
            isset($data['dateEcheance']) ? new \DateTime($data['dateEcheance']) : null
        );

        // ✅ Créateur = utilisateur connecté
        $tache->setCreateur($this->getUser());

        // ✅ Lien campagne
        if (!empty($data['campagneId'])) {
            $campagne = $campagneRepo->find($data['campagneId']);
            $tache->setCampagne($campagne);
        }

        // ✅ Assignation
        if (!empty($data['assigneA'])) {
            $user = $userRepo->find($data['assigneA']);
            $tache->setAssigneA($user);
        }

        $tache->setDateCreation(new \DateTime());

        $em->persist($tache);
        $em->flush();

        return $this->json($tache, 201, [], ['groups' => 'tache:detail']);
    }

    // ✅ EDIT TACHE
    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(
        Tache $tache,
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $userRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['titre'])) {
            $tache->setTitre($data['titre']);
        }

        if (isset($data['description'])) {
            $tache->setDescription($data['description']);
        }

        if (isset($data['statut'])) {
            $tache->setStatut($data['statut']);
        }

        if (isset($data['priorite'])) {
            $tache->setPriorite($data['priorite']);
        }

        if (isset($data['assigneA'])) {
            $user = $userRepo->find($data['assigneA']);
            $tache->setAssigneA($user);
        }

        $tache->setDateModification(new \DateTime());

        $em->flush();

        return $this->json($tache, 200, [], ['groups' => 'tache:detail']);
    }

    // ✅ DELETE TACHE
    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Tache $tache, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($tache);
        $em->flush();

        return $this->json([
            'message' => 'Tâche supprimée'
        ]);
    }
}