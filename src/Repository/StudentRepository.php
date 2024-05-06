<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    //    /**
    //     * @return Student[] Returns an array of Student objects
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

    /**
    * @return string Returns largest ID
    */
    public function findLargestStudentId(): string
    {
        $studentList = $this->createQueryBuilder('s')
            ->orderBy('s.student_id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
        if (sizeof($studentList) == 0) return '0';
        return $studentList[0]->getStudentId();
    }

    public function findOneByStudentId($studentId): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.student_id = :val')
            ->setParameter('val', $studentId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findBySchoolClass($id): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.school_class = :val')
            ->setParameter('val', $id)
            ->orderBy('s.lastname', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByParameters($searchData)
    {
        $query = $this
            ->createQueryBuilder('s');
        
        if (!empty($searchData['firstname'])) {
            $query = $query
                ->andWhere('s.firstname LIKE :val1')
                ->setParameter('val1', "%{$searchData['firstname']}%");
        }

        if (!empty($searchData['lastname'])) {
            $query = $query
                ->andWhere('s.lastname LIKE :val2')
                ->setParameter('val2', "%{$searchData['lastname']}%");
        }

        if (!empty($searchData['studentID'])) {
            $query = $query
                ->andWhere('s.student_id LIKE :val3')
                ->setParameter('val3', $searchData['studentID']);
        }

        if (!empty($searchData['classID'])) {
            $query = $query
                ->andWhere('s.school_class = :val4')
                ->setParameter('val4', $searchData['classID']);
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}
