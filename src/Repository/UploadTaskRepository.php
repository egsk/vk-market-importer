<?php

namespace App\Repository;

use App\Entity\UploadTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UploadTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadTask[]    findAll()
 * @method UploadTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadTask::class);
    }

    // /**
    //  * @return UploadTask[] Returns an array of UploadTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UploadTask
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
