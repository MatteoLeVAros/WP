<?php

namespace App\Service;

use App\Entity\CampagneValidation;
use App\Entity\Tache;
use App\Repository\CampagneValidationRepository;
use App\Repository\TacheRepository;

class PlanningService
{
    public function __construct(
        private TacheRepository $tacheRepository,
        private CampagneValidationRepository $campagneRepository
    ) {
    }

    public function getPlanning(array $filters = []): array
    {
        $items = [];

        foreach ($this->campagneRepository->findAll() as $campagne) {
            $items[] = $this->normalizeCampagne($campagne);
        }

        foreach ($this->tacheRepository->findAll() as $tache) {
            $items[] = $this->normalizeTache($tache);
        }

        $items = $this->applyFilters($items, $filters);

        usort($items, function (array $a, array $b) {
            $dateA = $a['dateDebut'] ?? $a['dateFin'] ?? null;
            $dateB = $b['dateDebut'] ?? $b['dateFin'] ?? null;

            if ($dateA === $dateB) {
                return 0;
            }

            if ($dateA === null) {
                return 1;
            }

            if ($dateB === null) {
                return -1;
            }

            return strcmp($dateA, $dateB);
        });

        return $items;
    }

    private function normalizeCampagne(CampagneValidation $campagne): array
    {
        return [
            'uid' => 'campagne-' . $campagne->getId(),
            'id' => $campagne->getId(),
            'type' => 'campagne',
            'titre' => $campagne->getTitre(),
            'reference' => $campagne->getReferenceCampagne(),
            'description' => $campagne->getDescription(),
            'statut' => $campagne->getStatut(),
            'priorite' => $campagne->getPriorite(),
            'dateDebut' => $campagne->getDateDebutReelle()
                ? $campagne->getDateDebutReelle()->format(DATE_ATOM)
                : ($campagne->getDateDebutPrevue()?->format(DATE_ATOM)),
            'dateFin' => $campagne->getDateFinReelle()
                ? $campagne->getDateFinReelle()->format(DATE_ATOM)
                : ($campagne->getDateFinPrevue()?->format(DATE_ATOM)),
            'responsable' => $campagne->getResponsable() ? [
                'id' => $campagne->getResponsable()->getId(),
                'nom' => $campagne->getResponsable()->getNom(),
                'prenom' => $campagne->getResponsable()->getPrenom(),
                'email' => $campagne->getResponsable()->getEmail(),
            ] : null,
            'campagneId' => $campagne->getId(),
            'tacheId' => null,
            'url' => '/campagnes/' . $campagne->getId(),
        ];
    }

    private function normalizeTache(Tache $tache): array
    {
        return [
            'uid' => 'tache-' . $tache->getId(),
            'id' => $tache->getId(),
            'type' => 'tache',
            'titre' => $tache->getTitre(),
            'reference' => null,
            'description' => $tache->getDescription(),
            'statut' => $tache->getStatut(),
            'priorite' => $tache->getPriorite(),
            'dateDebut' => $tache->getDateDebut()
                ? $tache->getDateDebut()->format(DATE_ATOM)
                : ($tache->getDateCreation()?->format(DATE_ATOM)),
            'dateFin' => $tache->getDateEcheance()
                ? $tache->getDateEcheance()->format(DATE_ATOM)
                : ($tache->getDateFin()?->format(DATE_ATOM)),
            'responsable' => $tache->getAssigneA() ? [
                'id' => $tache->getAssigneA()->getId(),
                'nom' => $tache->getAssigneA()->getNom(),
                'prenom' => $tache->getAssigneA()->getPrenom(),
                'email' => $tache->getAssigneA()->getEmail(),
            ] : null,
            'campagneId' => $tache->getCampagne()?->getId(),
            'tacheId' => $tache->getId(),
            'url' => '/taches/' . $tache->getId(),
        ];
    }

    private function applyFilters(array $items, array $filters): array
    {
        return array_values(array_filter($items, function (array $item) use ($filters) {
            if (!empty($filters['type']) && $item['type'] !== $filters['type']) {
                return false;
            }

            if (!empty($filters['statut']) && $item['statut'] !== $filters['statut']) {
                return false;
            }

            if (!empty($filters['priorite']) && $item['priorite'] !== $filters['priorite']) {
                return false;
            }

            if (!empty($filters['responsable']) && $item['type'] === 'campagne') {
                if (($item['responsable']['id'] ?? null) != $filters['responsable']) {
                    return false;
                }
            }

            if (!empty($filters['assigneA']) && $item['type'] === 'tache') {
                if (($item['responsable']['id'] ?? null) != $filters['assigneA']) {
                    return false;
                }
            }

            if (!empty($filters['search'])) {
                $search = mb_strtolower($filters['search']);
                $haystack = mb_strtolower(
                    ($item['titre'] ?? '') . ' ' .
                    ($item['reference'] ?? '') . ' ' .
                    ($item['description'] ?? '')
                );

                if (!str_contains($haystack, $search)) {
                    return false;
                }
            }

            if (!empty($filters['from'])) {
                $from = strtotime($filters['from']);
                $itemDate = $item['dateDebut'] ? strtotime($item['dateDebut']) : null;

                if ($itemDate !== null && $itemDate < $from) {
                    return false;
                }
            }

            if (!empty($filters['to'])) {
                $to = strtotime($filters['to']);
                $itemDate = $item['dateFin']
                    ? strtotime($item['dateFin'])
                    : ($item['dateDebut'] ? strtotime($item['dateDebut']) : null);

                if ($itemDate !== null && $itemDate > $to) {
                    return false;
                }
            }

            return true;
        }));
    }
}
