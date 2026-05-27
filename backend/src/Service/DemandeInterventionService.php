<?php

namespace App\Service;

use App\Entity\DemandeIntervention;
use App\Entity\Utilisateur;
use App\Repository\DemandeInterventionRepository;
use App\Repository\TypeTestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DemandeInterventionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DemandeInterventionRepository $demandeRepository,
        private TypeTestRepository $typeTestRepository
    ) {
    }

    public function findForUser(Utilisateur $user): array
    {
        return $this->demandeRepository->findForUser($user->getId());
    }

    public function findOne(int $id): DemandeIntervention
    {
        $demande = $this->demandeRepository->find($id);

        if (!$demande) {
            throw new NotFoundHttpException('Demande introuvable');
        }

        return $demande;
    }

    public function create(array $data, Utilisateur $demandeur): DemandeIntervention
    {
        $required = [
            'typeIntervention',
            'projetNumeroMoyenValidation',
            'systeme',
            'emplacementMoyenBadge',
            'dureeIntervention',
            'dateDemarrageSouhaitee',
            'dateLimiteLivraison',
            'nombreIntervenants',
        ];

        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new BadRequestHttpException(sprintf('Le champ "%s" est obligatoire.', $field));
            }
        }

        $demande = new DemandeIntervention();
        $demande->setTypeIntervention($data['typeIntervention']);
        $demande->setProjetNumeroMoyenValidation($data['projetNumeroMoyenValidation']);
        $demande->setSysteme($data['systeme']);
        $demande->setEmplacementMoyenBadge($data['emplacementMoyenBadge']);
        $demande->setDureeIntervention($data['dureeIntervention']);
        $demande->setDateDemarrageSouhaitee(new \DateTime($data['dateDemarrageSouhaitee']));
        $demande->setDateLimiteLivraison(new \DateTime($data['dateLimiteLivraison']));
        $demande->setNombreIntervenants((int) $data['nombreIntervenants']);
        $demande->setBesoinConducteurPermisC($data['besoinConducteurPermisC'] ?? null);
        $demande->setLienStockagePvalLogs($data['lienStockagePvalLogs'] ?? null);
        $demande->setStatutInstrumentation($data['statutInstrumentation'] ?? null);
        $demande->setLienTemplateChecklist($data['lienTemplateChecklist'] ?? null);
        $demande->setVersionSwValider($data['versionSwValider'] ?? null);
        $demande->setCanEnregistrer($data['canEnregistrer'] ?? null);
        $demande->setStatutDemande('soumise');
        $demande->setDateCreation(new \DateTime());
        $demande->setDemandeur($demandeur);

        if (!empty($data['typeTestIds']) && is_array($data['typeTestIds'])) {
            foreach ($data['typeTestIds'] as $id) {
                $typeTest = $this->typeTestRepository->find($id);
                if ($typeTest) {
                    $demande->addTypeTest($typeTest);
                }
            }
        }

        if ($demande->getDateLimiteLivraison() < $demande->getDateDemarrageSouhaitee()) {
            throw new BadRequestHttpException('La date limite de livraison doit être >= à la date de démarrage souhaitée.');
        }

        $this->em->persist($demande);
        $this->em->flush();

        return $demande;
    }

    public function update(int $id, array $data, Utilisateur $user): DemandeIntervention
    {
        $demande = $this->findOne($id);

        if ($demande->getDemandeur()?->getId() !== $user->getId()
            && !in_array('ROLE_ADMIN', $user->getRoles(), true)
        ) {
            throw new BadRequestHttpException('Vous ne pouvez pas modifier cette demande.');
        }

        if (isset($data['typeIntervention'])) $demande->setTypeIntervention($data['typeIntervention']);
        if (isset($data['projetNumeroMoyenValidation'])) $demande->setProjetNumeroMoyenValidation($data['projetNumeroMoyenValidation']);
        if (isset($data['systeme'])) $demande->setSysteme($data['systeme']);
        if (isset($data['emplacementMoyenBadge'])) $demande->setEmplacementMoyenBadge($data['emplacementMoyenBadge']);
        if (isset($data['dureeIntervention'])) $demande->setDureeIntervention($data['dureeIntervention']);
        if (isset($data['dateDemarrageSouhaitee'])) $demande->setDateDemarrageSouhaitee(new \DateTime($data['dateDemarrageSouhaitee']));
        if (isset($data['dateLimiteLivraison'])) $demande->setDateLimiteLivraison(new \DateTime($data['dateLimiteLivraison']));
        if (isset($data['nombreIntervenants'])) $demande->setNombreIntervenants((int) $data['nombreIntervenants']);
        if (array_key_exists('besoinConducteurPermisC', $data)) $demande->setBesoinConducteurPermisC($data['besoinConducteurPermisC']);
        if (array_key_exists('lienStockagePvalLogs', $data)) $demande->setLienStockagePvalLogs($data['lienStockagePvalLogs']);
        if (array_key_exists('statutInstrumentation', $data)) $demande->setStatutInstrumentation($data['statutInstrumentation']);
        if (array_key_exists('lienTemplateChecklist', $data)) $demande->setLienTemplateChecklist($data['lienTemplateChecklist']);
        if (array_key_exists('versionSwValider', $data)) $demande->setVersionSwValider($data['versionSwValider']);
        if (array_key_exists('canEnregistrer', $data)) $demande->setCanEnregistrer($data['canEnregistrer']);
        if (array_key_exists('statutDemande', $data) && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $demande->setStatutDemande($data['statutDemande']);
        }

        $demande->setDateModification(new \DateTime());

        $this->em->flush();

        return $demande;
    }

    public function delete(int $id, Utilisateur $user): void
    {
        $demande = $this->findOne($id);

        if ($demande->getDemandeur()?->getId() !== $user->getId()
            && !in_array('ROLE_ADMIN', $user->getRoles(), true)
        ) {
            throw new BadRequestHttpException('Vous ne pouvez pas supprimer cette demande.');
        }

        $this->em->remove($demande);
        $this->em->flush();
    }
}