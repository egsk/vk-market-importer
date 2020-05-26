<?php

namespace App\Repository;

use App\Entity\CsvLinkDataSourceVkProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CsvLinkDataSourceVkProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method CsvLinkDataSourceVkProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method CsvLinkDataSourceVkProduct[]    findAll()
 * @method CsvLinkDataSourceVkProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CsvLinkDataSourceVkProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CsvLinkDataSourceVkProduct::class);
    }

    // /**
    //  * @return CsvLinkDataSourceVkProduct[] Returns an array of CsvLinkDataSourceVkProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CsvLinkDataSourceVkProduct
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
