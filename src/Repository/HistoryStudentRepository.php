<?php

namespace App\Repository;

use App\Entity\HistoryStudent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryStudent>
 *
 * @method HistoryStudent|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryStudent|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryStudent[]    findAll()
 * @method HistoryStudent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryStudent::class);
    }

    //    /**
    //     * @return HistoryStudent[] Returns an array of HistoryStudent objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HistoryStudent
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
