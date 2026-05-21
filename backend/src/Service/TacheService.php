<?php

namespace App\Service;

use App\Entity\Tache;
use App\Repository\TacheRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\CampagneValidationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TacheService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TacheRepository $tacheRepository,
        private UtilisateurRepository $userRepository,
        private CampagneValidationRepository $campagneRepository,
        private NotificationService $notificationService
    ) {}

    // ✅ CREATE
    public function create(array $data): Tache
    {
        $tache = new Tache();

        $tache->setTitre($data['titre']);
        $tache->setDescription($data['description'] ?? null);
        $tache->setStatut($data['statut']);
        $tache->setPriorite($data['priorite'] ?? null);

        $tache->setDateCreation(new \DateTime());

        // Dates
        if (!empty($data['dateDebut'])) {
            $tache->setDateDebut(new \DateTime($data['dateDebut']));
        }

        if (!empty($data['dateEcheance'])) {
            $tache->setDateEcheance(new \DateTime($data['dateEcheance']));
        }

        // ✅ Créateur (déjà injecté par controller)
        $tache->setCreateur($data['createur']);

        // ✅ Campagne
        if (!empty($data['campagneId'])) {
            $campagne = $this->campagneRepository->find($data['campagneId']);
            if (!$campagne) {
                throw new NotFoundHttpException('Campagne introuvable');
            }
            $tache->setCampagne($campagne);
        }

        // ✅ Assignation
        if (!empty($data['assigneAId'])) {
            $user = $this->userRepository->find($data['assigneAId']);
            if (!$user) {
                throw new NotFoundHttpException('Utilisateur introuvable');
            }
            $tache->setAssigneA($user);
        }

        // ✅ Validation dates
        $this->validateDates($tache);

        $this->em->persist($tache);
        $this->em->flush();

        // ✅ Notification
        if ($tache->getAssigneA()) {
            $this->notificationService->notifyTaskAssigned($tache);
        }

        return $tache;
    }

    // ✅ UPDATE
    public function update(int $id, array $data): Tache
    {
        $tache = $this->tacheRepository->find($id);

        if (!$tache) {
            throw new NotFoundHttpException('Tâche introuvable');
        }

        $oldUserId = $tache->getAssigneA()?->getId();

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

        if (isset($data['dateDebut'])) {
            $tache->setDateDebut(new \DateTime($data['dateDebut']));
        }

        if (isset($data['dateEcheance'])) {
            $tache->setDateEcheance(new \DateTime($data['dateEcheance']));
        }

        if (isset($data['assigneAId'])) {
            $user = $this->userRepository->find($data['assigneAId']);
            if (!$user) {
                throw new NotFoundHttpException('Utilisateur introuvable');
            }
            $tache->setAssigneA($user);
        }

        $this->validateDates($tache);

        $tache->setDateModification(new \DateTime());
        $this->em->flush();

        // ✅ Si changement d'assignation → notification
        $newUserId = $tache->getAssigneA()?->getId();
        if ($newUserId && $newUserId !== $oldUserId) {
            $this->notificationService->notifyTaskAssigned($tache);
        }

        return $tache;
    }

    // ✅ DELETE
    public function delete(int $id): void
    {
        $tache = $this->tacheRepository->find($id);

        if (!$tache) {
            throw new NotFoundHttpException('Tâche introuvable');
        }

        $this->em->remove($tache);
        $this->em->flush();
    }

    // ✅ FIND ALL
    public function findAll(): array
    {
        return $this->tacheRepository->findBy([], ['dateCreation' => 'DESC']);
    }

    // ✅ FIND ONE
    public function findOne(int $id): Tache
    {
        $tache = $this->tacheRepository->find($id);

        if (!$tache) {
            throw new NotFoundHttpException('Tâche introuvable');
        }

        return $tache;
    }

    // ✅ VALIDATION METIER
    private function validateDates(Tache $tache): void
    {
        $d = $tache->getDateDebut();
        $f = $tache->getDateFin();
        $e = $tache->getDateEcheance();

        if ($d && $f && $f < $d) {
            throw new BadRequestHttpException('Date fin < date début');
        }

        if ($d && $e && $e < $d) {
            throw new BadRequestHttpException('Date échéance < date début');
        }
    }
}