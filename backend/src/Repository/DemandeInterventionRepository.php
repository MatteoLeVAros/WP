<?php

namespace App\Repository;

use App\Entity\DemandeIntervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeIntervention>
 */
class DemandeInterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeIntervention::class);
    }

//    /**
//     * @return DemandeIntervention[] Returns an array of DemandeIntervention objects
//     */
    public function findForUser(int $userId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.demandeur = :user')
            ->setParameter('user', $userId)
            ->orderBy('d.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();

   }

//    public function findOneBySomeField($value): ?DemandeIntervention
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
