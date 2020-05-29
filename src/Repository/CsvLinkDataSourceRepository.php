<?php

namespace App\Repository;

use App\Entity\CsvLinkDataSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CsvLinkDataSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method CsvLinkDataSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method CsvLinkDataSource[]    findAll()
 * @method CsvLinkDataSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CsvLinkDataSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CsvLinkDataSource::class);
    }

    // /**
    //  * @return RepresentationProvider[] Returns an array of RepresentationProvider objects
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
    public function findOneBySomeField($value): ?RepresentationProvider
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
