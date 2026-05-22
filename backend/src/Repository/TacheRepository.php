<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }
        public function search(array $filters): array
        {
            $qb = $this->createQueryBuilder('t');

            if (!empty($filters['statut'])) {
                $qb->andWhere('t.statut = :statut')
                    ->setParameter('statut', $filters['statut']);
            }

            if (!empty($filters['priorite'])) {
                $qb->andWhere('t.priorite = :priorite')
                    ->setParameter('priorite', $filters['priorite']);
            }

            if (!empty($filters['assigneA'])) {
                $qb->andWhere('t.assigneA = :assigneA')
                    ->setParameter('assigneA', $filters['assigneA']);
            }

            if (!empty($filters['campagne'])) {
                $qb->andWhere('t.campagne = :campagne')
                    ->setParameter('campagne', $filters['campagne']);
            }

            if (!empty($filters['search'])) {
                $qb->andWhere('t.titre LIKE :search')
                    ->setParameter('search', '%' . $filters['search'] . '%');
            }

            return $qb
                ->orderBy('t.dateCreation', 'DESC')
                ->getQuery()
                ->getResult();
        }

//    /**
//     * @return Tache[] Returns an array of Tache objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tache
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
