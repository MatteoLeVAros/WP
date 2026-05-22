<?php

namespace App\Service;

use App\Entity\CampagneValidation;
use App\Entity\Utilisateur;
use App\Repository\CampagneValidationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CampagneValidationService
{
    private const STATUTS = [
        'brouillon',
        'planifiee',
        'en_cours',
        'terminee',
        'annulee',
    ];

    private const PRIORITES = [
        'basse',
        'moyenne',
        'haute',
        'critique',
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private CampagneValidationRepository $campagneRepository,
        private UtilisateurRepository $utilisateurRepository
    ) {
    }

    public function search(array $filters): array
    {
        return $this->campagneRepository->search($filters);
    }

    public function findOne(int $id): CampagneValidation
    {
        $campagne = $this->campagneRepository->find($id);

        if (!$campagne) {
            throw new NotFoundHttpException('Campagne introuvable');
        }

        return $campagne;
    }

    public function create(array $data): CampagneValidation
    {
        $this->validateRequiredFields($data);

        $campagne = new CampagneValidation();

        $campagne->setReferenceCampagne($data['referenceCampagne']);
        $campagne->setTitre($data['titre']);
        $campagne->setDescription($data['description'] ?? null);
        $campagne->setStatut($this->validateStatut($data['statut']));
        $campagne->setPriorite($this->validatePriorite($data['priorite'] ?? null));
        $campagne->setDateDebutPrevue(new \DateTime($data['dateDebutPrevue']));
        $campagne->setDateFinPrevue(new \DateTime($data['dateFinPrevue']));
        $campagne->setDateCreation(new \DateTime());

        if (!empty($data['dateDebutReelle'])) {
            $campagne->setDateDebutReelle(new \DateTime($data['dateDebutReelle']));
        }

        if (!empty($data['dateFinReelle'])) {
            $campagne->setDateFinReelle(new \DateTime($data['dateFinReelle']));
        }

        if (array_key_exists('commentaireGlobal', $data)) {
            $campagne->setCommentaireGlobal($data['commentaireGlobal']);
        }

        $responsable = $this->resolveResponsable($data);
        $campagne->setResponsable($responsable);

        $this->validateDates($campagne);
        $this->ensureReferenceIsUnique($campagne->getReferenceCampagne());

        $this->em->persist($campagne);
        $this->em->flush();

        return $campagne;
    }

    public function update(int $id, array $data): CampagneValidation
    {
        $campagne = $this->campagneRepository->find($id);

        if (!$campagne) {
            throw new NotFoundHttpException('Campagne introuvable');
        }

        if (array_key_exists('referenceCampagne', $data)) {
            $reference = trim((string) $data['referenceCampagne']);
            if ($reference === '') {
                throw new BadRequestHttpException('La référence campagne ne peut pas être vide.');
            }

            if ($reference !== $campagne->getReferenceCampagne()) {
                $this->ensureReferenceIsUnique($reference);
            }

            $campagne->setReferenceCampagne($reference);
        }

        if (array_key_exists('titre', $data)) {
            $titre = trim((string) $data['titre']);
            if ($titre === '') {
                throw new BadRequestHttpException('Le titre ne peut pas être vide.');
            }

            $campagne->setTitre($titre);
        }

        if (array_key_exists('description', $data)) {
            $campagne->setDescription($data['description']);
        }

        if (array_key_exists('statut', $data)) {
            $campagne->setStatut($this->validateStatut($data['statut']));
        }

        if (array_key_exists('priorite', $data)) {
            $campagne->setPriorite($this->validatePriorite($data['priorite']));
        }

        if (array_key_exists('dateDebutPrevue', $data)) {
            if (empty($data['dateDebutPrevue'])) {
                throw new BadRequestHttpException('La dateDebutPrevue est obligatoire.');
            }

            $campagne->setDateDebutPrevue(new \DateTime($data['dateDebutPrevue']));
        }

        if (array_key_exists('dateFinPrevue', $data)) {
            if (empty($data['dateFinPrevue'])) {
                throw new BadRequestHttpException('La dateFinPrevue est obligatoire.');
            }

            $campagne->setDateFinPrevue(new \DateTime($data['dateFinPrevue']));
        }

        if (array_key_exists('dateDebutReelle', $data)) {
            $campagne->setDateDebutReelle(
                $data['dateDebutReelle'] ? new \DateTime($data['dateDebutReelle']) : null
            );
        }

        if (array_key_exists('dateFinReelle', $data)) {
            $campagne->setDateFinReelle(
                $data['dateFinReelle'] ? new \DateTime($data['dateFinReelle']) : null
            );
        }

        if (array_key_exists('commentaireGlobal', $data)) {
            $campagne->setCommentaireGlobal($data['commentaireGlobal']);
        }

        if (array_key_exists('responsableId', $data) || array_key_exists('responsable', $data)) {
            $campagne->setResponsable($this->resolveResponsable($data));
        }

        $campagne->setDateModification(new \DateTime());

        $this->validateDates($campagne);

        $this->em->flush();

        return $campagne;
    }

    public function delete(int $id): void
    {
        $campagne = $this->campagneRepository->find($id);

        if (!$campagne) {
            throw new NotFoundHttpException('Campagne introuvable');
        }

        $this->em->remove($campagne);
        $this->em->flush();
    }

    private function validateRequiredFields(array $data): void
    {
        $requiredFields = [
            'referenceCampagne',
            'titre',
            'statut',
            'dateDebutPrevue',
            'dateFinPrevue',
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                throw new BadRequestHttpException(sprintf('Le champ "%s" est obligatoire.', $field));
            }
        }

        if (!array_key_exists('responsableId', $data) && !array_key_exists('responsable', $data)) {
            throw new BadRequestHttpException('Le responsable est obligatoire.');
        }
    }

    private function validateStatut(?string $statut): string
    {
        if (!$statut || !in_array($statut, self::STATUTS, true)) {
            throw new BadRequestHttpException(
                'Statut invalide. Valeurs autorisées : '.implode(', ', self::STATUTS)
            );
        }

        return $statut;
    }

    private function validatePriorite(?string $priorite): ?string
    {
        if ($priorite === null || $priorite === '') {
            return null;
        }

        if (!in_array($priorite, self::PRIORITES, true)) {
            throw new BadRequestHttpException(
                'Priorité invalide. Valeurs autorisées : '.implode(', ', self::PRIORITES)
            );
        }

        return $priorite;
    }

    private function validateDates(CampagneValidation $campagne): void
    {
        $dateDebutPrevue = $campagne->getDateDebutPrevue();
        $dateFinPrevue = $campagne->getDateFinPrevue();
        $dateDebutReelle = $campagne->getDateDebutReelle();
        $dateFinReelle = $campagne->getDateFinReelle();

        if ($dateDebutPrevue && $dateFinPrevue && $dateFinPrevue < $dateDebutPrevue) {
            throw new BadRequestHttpException(
                'La date de fin prévue doit être supérieure ou égale à la date de début prévue.'
            );
        }

        if ($dateDebutReelle && $dateFinReelle && $dateFinReelle < $dateDebutReelle) {
            throw new BadRequestHttpException(
                'La date de fin réelle doit être supérieure ou égale à la date de début réelle.'
            );
        }
    }

    private function resolveResponsable(array $data): Utilisateur
    {
        if (isset($data['responsable']) && $data['responsable'] instanceof Utilisateur) {
            return $data['responsable'];
        }

        if (!isset($data['responsableId']) || !$data['responsableId']) {
            throw new BadRequestHttpException('Le responsableId est obligatoire.');
        }

        $responsable = $this->utilisateurRepository->find($data['responsableId']);

        if (!$responsable) {
            throw new NotFoundHttpException('Responsable introuvable');
        }

        return $responsable;
    }

    private function ensureReferenceIsUnique(string $referenceCampagne): void
    {
        $existing = $this->campagneRepository->findOneBy([
            'referenceCampagne' => $referenceCampagne,
        ]);

        if ($existing) {
            throw new BadRequestHttpException('Une campagne avec cette référence existe déjà.');
        }
    }
}