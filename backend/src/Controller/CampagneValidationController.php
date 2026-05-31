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
use App\Entity\Tache;
use App\Repository\TacheRepository;
use App\Entity\Utilisateur;
use App\Repository\CommentaireRepository;

#[Route('/api/campagnes')]
#[IsGranted('ROLE_ADMIN')]
class CampagneValidationController extends AbstractController
{
    // ✅ LISTE DES CAMPAGNES
    #[Route('', methods: ['GET'])]
    public function index(Request $request, CampagneValidationRepository $campagneValidationRepository): JsonResponse 
    {
        $filters = [
            'statut' => $request->query->get('statut'),
            'priorite' => $request->query->get('priorite'),
            'responsable' => $request->query->get('responsable'),
            'search' => $request->query->get('search'),
            'assigned' => $request->query->get('assigned'),
        ];
        $campagnes = $campagneValidationRepository->search($filters);

        return $this->json($campagnes, 200, [], ['groups' => ['campagne:list']]);
    }

    // ✅ DETAIL CAMPAGNE
    #[Route('/{id}', name: 'campagne_show', methods: ['GET'])]
    public function show(CampagneValidation $campagne): JsonResponse
    {
        return $this->json($campagne, 200, [], ['groups' => ['campagne:detail']]);
    }

    // ✅ CREATION CAMPAGNE
    #[Route('', name: 'campagne_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $userRepo,
        \App\Repository\DemandeInterventionRepository $demandeRepo
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }
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
            $campagne->setResponsable($user);
        }

        // Liaison avec une demande d’intervention
        $demande = null;
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
        // ✅ Création automatique de la tâche
        $tache = new Tache();
        $tache->setTitre('Intervention - ' . $campagne->getTitre());

        $tache->setDescription(sprintf(
            "Campagne : %s\nRéférence : %s\nDemande liée : %s",
            $campagne->getTitre(),
            $campagne->getReferenceCampagne(),
            !empty($demande) ? ('#' . $demande->getId()) : 'aucune'
        ));

        $tache->setStatut('a_faire'); // ou 'planifiee' selon ton workflow
        $tache->setPriorite($campagne->getPriorite());
        $tache->setDateDebut($campagne->getDateDebutPrevue());
        $tache->setDateEcheance($campagne->getDateFinPrevue());
        $tache->setDateCreation(new \DateTime());
        $tache->setCampagne($campagne);
        $tache->setAssigneA($campagne->getResponsable());
        $tache->setCreateur($user);

        $em->persist($tache);


        $em->persist($campagne);
        $em->flush();

        return $this->json($campagne, 201, [], ['groups' => ['campagne:detail']]);
    }

    // ✅ UPDATE CAMPAGNE
    #[Route('/{id}', name: 'campagne_update', methods: ['PUT'])]
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

    #[Route('/{id}', name: 'campagne_delete', methods: ['DELETE'])]
    public function delete(
        CampagneValidation $campagne,
        EntityManagerInterface $em,
        TacheRepository $tacheRepository,
        CommentaireRepository $commentaireRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // 1) Supprimer les tâches liées
        $taches = $tacheRepository->findByCampagne($campagne);
        foreach ($taches as $tache) {
            $em->remove($tache);
        }

        // 2) Supprimer les commentaires liés
        $commentaires = $commentaireRepository->findByCampagne($campagne);
        foreach ($commentaires as $commentaire) {
            $em->remove($commentaire);
        }

        // 3) Détacher les demandes liées et les remettre "soumise"
        foreach ($campagne->getDemandeInterventions() as $demande) {
            $demande->setCampagne(null);
            $demande->setStatutDemande('soumise');
            $demande->setDateModification(new \DateTime());
        }

        // 4) Supprimer la campagne
        $em->remove($campagne);
        $em->flush();

        return $this->json([
            'message' => 'Campagne supprimée'
        ], 200);
    }
}