<?php

namespace App\Repository;

use App\Entity\ImportTarget;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImportTarget|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportTarget|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportTarget[]    findAll()
 * @method ImportTarget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportTargetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportTarget::class);
    }

    public function findWithDataSource(?User $user = null)
    {
        $queryBuilder = $this->createQueryBuilder('it')
            ->leftJoin('it.csvLinkDataSources', 'clds')
            ->orderBy('it.id', 'DESC');

        if ($user) {
            $queryBuilder
                ->andWhere('it.user = :user')
                ->setParameter('user', $user);
        }

        return $queryBuilder
            ->getQuery()
            ->execute();
    }

    // /**
    //  * @return ImportTarget[] Returns an array of ImportTarget objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImportTarget
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
