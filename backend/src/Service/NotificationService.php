<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Tache;
use App\Entity\Utilisateur;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private NotificationRepository $notificationRepository
    ) {
    }

    public function notifyTaskAssigned(Tache $tache): Notification
    {
        $assigne = $tache->getAssigneA();

        if (!$assigne) {
            throw new NotFoundHttpException('Impossible de créer une notification : aucun utilisateur assigné.');
        }

        $notification = new Notification();
        $notification->setTypeNotification('assignation_tache');
        $notification->setTitre('Nouvelle tâche assignée');
        $notification->setMessage(sprintf(
            'La tâche "%s" vous a été assignée.',
            $tache->getTitre()
        ));
        $notification->setCanal('app');
        $notification->setEstLue(false);
        $notification->setDateEnvoi(new \DateTime());
        $notification->setUtilisateur($assigne);
        $notification->setTache($tache);

        if ($tache->getCampagne()) {
            $notification->setCampagne($tache->getCampagne());
        }

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }

    public function findForUser(Utilisateur $utilisateur): array
    {
        return $this->notificationRepository->findBy(
            ['utilisateur' => $utilisateur],
            ['dateEnvoi' => 'DESC']
        );
    }

    public function markAsRead(int $id, Utilisateur $utilisateur): Notification
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification) {
            throw new NotFoundHttpException('Notification introuvable');
        }

        if ($notification->getUtilisateur()?->getId() !== $utilisateur->getId()) {
            throw new NotFoundHttpException('Notification introuvable');
        }

        $notification->setEstLue(true);
        $notification->setDateLecture(new \DateTime());

        $this->em->flush();

        return $notification;
    }

    public function findOneForUser(int $id, Utilisateur $utilisateur): Notification
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification) {
            throw new NotFoundHttpException('Notification introuvable');
        }

        if ($notification->getUtilisateur()?->getId() !== $utilisateur->getId()) {
            throw new NotFoundHttpException('Notification introuvable');
        }

        return $notification;
    }
}