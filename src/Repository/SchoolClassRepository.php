<?php

namespace App\Repository;

use App\Entity\SchoolClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolClass>
 *
 * @method SchoolClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolClass[]    findAll()
 * @method SchoolClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolClass::class);
    }

    //    /**
    //     * @return SchoolClass[] Returns an array of SchoolClass objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findAllOrderedByYear(): array
       {
           return $this->createQueryBuilder('s')
               ->orderBy('s.promotion_year', 'DESC')
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByParameters($searchData)
    {
        $query = $this
            ->createQueryBuilder('s');
        
        if (!empty($searchData['year'])) {
            $query = $query
                ->andWhere('s.promotion_year LIKE :val1')
                ->setParameter('val1', "%{$searchData['year']}%");
        }

        if (!empty($searchData['teacher_name'])) {
            $query = $query
                ->andWhere('s.teacher_name LIKE :val2')
                ->setParameter('val2', "%{$searchData['teacher_name']}%");
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}
