<?php

namespace App\Service;

use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Repository\CampagneValidationRepository;
use App\Repository\CommentaireRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentaireService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CommentaireRepository $commentaireRepository,
        private TacheRepository $tacheRepository,
        private CampagneValidationRepository $campagneRepository
    ) {
    }

    public function create(array $data, Utilisateur $auteur): Commentaire
    {
        if (empty($data['contenu'])) {
            throw new BadRequestHttpException('Le contenu du commentaire est obligatoire.');
        }

        $hasTache = !empty($data['tacheId']);
        $hasCampagne = !empty($data['campagneId']);

        if (($hasTache && $hasCampagne) || (!$hasTache && !$hasCampagne)) {
            throw new BadRequestHttpException(
                'Le commentaire doit être lié soit à une tâche, soit à une campagne.'
            );
        }

        $commentaire = new Commentaire();
        $commentaire->setContenu(trim($data['contenu']));
        $commentaire->setDateCreation(new \DateTime());
        $commentaire->setModere(false);
        $commentaire->setAuteur($auteur);

        if ($hasTache) {
            $tache = $this->tacheRepository->find($data['tacheId']);

            if (!$tache) {
                throw new NotFoundHttpException('Tâche introuvable');
            }

            $commentaire->setTache($tache);
            $commentaire->setCampagne(null);
        }

        if ($hasCampagne) {
            $campagne = $this->campagneRepository->find($data['campagneId']);

            if (!$campagne) {
                throw new NotFoundHttpException('Campagne introuvable');
            }

            $commentaire->setCampagne($campagne);
            $commentaire->setTache(null);
        }

        $this->em->persist($commentaire);
        $this->em->flush();

        return $commentaire;
    }

    public function findOne(int $id): Commentaire
    {
        $commentaire = $this->commentaireRepository->find($id);

        if (!$commentaire) {
            throw new NotFoundHttpException('Commentaire introuvable');
        }

        return $commentaire;
    }

    public function findByTache(int $tacheId): array
    {
        $tache = $this->tacheRepository->find($tacheId);

        if (!$tache) {
            throw new NotFoundHttpException('Tâche introuvable');
        }

        return $this->commentaireRepository->findBy(
            ['tache' => $tache],
            ['dateCreation' => 'DESC']
        );
    }

    public function findByCampagne(int $campagneId): array
    {
        $campagne = $this->campagneRepository->find($campagneId);

        if (!$campagne) {
            throw new NotFoundHttpException('Campagne introuvable');
        }

        return $this->commentaireRepository->findBy(
            ['campagne' => $campagne],
            ['dateCreation' => 'DESC']
        );
    }

    public function update(int $id, array $data, Utilisateur $user): Commentaire
    {
        $commentaire = $this->commentaireRepository->find($id);

        if (!$commentaire) {
            throw new NotFoundHttpException('Commentaire introuvable');
        }

        if (
            $commentaire->getAuteur()?->getId() !== $user->getId()
            && !in_array('ROLE_ADMIN', $user->getRoles(), true)
        ) {
            throw new BadRequestHttpException('Vous ne pouvez pas modifier ce commentaire.');
        }

        if (empty($data['contenu'])) {
            throw new BadRequestHttpException('Le contenu du commentaire est obligatoire.');
        }

        $commentaire->setContenu(trim($data['contenu']));
        $commentaire->setDateModification(new \DateTime());

        $this->em->flush();

        return $commentaire;
    }

    public function delete(int $id, Utilisateur $user): void
    {
        $commentaire = $this->commentaireRepository->find($id);

        if (!$commentaire) {
            throw new NotFoundHttpException('Commentaire introuvable');
        }

        if (
            $commentaire->getAuteur()?->getId() !== $user->getId()
            && !in_array('ROLE_ADMIN', $user->getRoles(), true)
        ) {
            throw new BadRequestHttpException('Vous ne pouvez pas supprimer ce commentaire.');
        }

        $this->em->remove($commentaire);
        $this->em->flush();
    }
}