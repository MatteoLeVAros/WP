<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Tache;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function notifyTaskAssigned(Tache $tache): void
    {
        $assigne = $tache->getAssigneA();
        if (!$assigne) {
            return;
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
        $notification->setDateEnvoi(new \DateTimeImmutable());
        $notification->setUtilisateur($assigne);
        $notification->setTache($tache);

        if ($tache->getCampagne()) {
            $notification->setCampagne($tache->getCampagne());
        }

        $this->em->persist($notification);
        $this->em->flush();
    }
}