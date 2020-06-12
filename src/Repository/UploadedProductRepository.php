<?php

namespace App\Repository;

use App\Entity\UploadedProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UploadedProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadedProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadedProduct[]    findAll()
 * @method UploadedProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadedProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadedProduct::class);
    }

    // /**
    //  * @return UploadedProduct[] Returns an array of UploadedProduct objects
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
    public function findOneBySomeField($value): ?UploadedProduct
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
