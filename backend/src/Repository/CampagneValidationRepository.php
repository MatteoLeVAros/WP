<?php

namespace App\Repository;

use App\Entity\CampagneValidation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampagneValidation>
 */
class CampagneValidationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampagneValidation::class);
    }
        public function search(array $filters): array
        {
            $qb = $this->createQueryBuilder('c');

            if (!empty($filters['statut'])) {
                $qb->andWhere('c.statut = :statut')
                    ->setParameter('statut', $filters['statut']);
            }

            if (!empty($filters['priorite'])) {
                $qb->andWhere('c.priorite = :priorite')
                    ->setParameter('priorite', $filters['priorite']);
            }

            if (!empty($filters['responsable'])) {
                $qb->andWhere('c.responsable = :responsable')
                    ->setParameter('responsable', $filters['responsable']);
            }

            if (!empty($filters['search'])) {
                $qb->andWhere('c.titre LIKE :search')
                    ->setParameter('search', '%' . $filters['search'] . '%');
            }

            if (!empty($filters['assigned']) && $filters['assigned'] == 1) {
                $qb->innerJoin('c.demandeInterventions', 'd')
                    ->addSelect('d');
            }


            return $qb
                ->orderBy('c.dateCreation', 'DESC')
                ->getQuery()
                ->getResult();
        }
    //    /**
    //     * @return CampagneValidation[] Returns an array of CampagneValidation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CampagneValidation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
