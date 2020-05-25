<?php

namespace App\Repository;

use App\Entity\VkMarketCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VkMarketCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method VkMarketCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method VkMarketCategory[]    findAll()
 * @method VkMarketCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VkMarketCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VkMarketCategory::class);
    }

    // /**
    //  * @return VkMarketCategory[] Returns an array of VkMarketCategory objects
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
    public function findOneBySomeField($value): ?VkMarketCategory
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
