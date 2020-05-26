<?php

namespace App\Repository;

use App\Entity\VkProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VkProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method VkProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method VkProduct[]    findAll()
 * @method VkProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VkProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VkProduct::class);
    }

    // /**
    //  * @return VkProduct[] Returns an array of VkProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VkProduct
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
