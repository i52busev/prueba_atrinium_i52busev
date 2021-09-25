<?php

namespace App\Repository;

use App\Entity\ClienteSector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClienteSector|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClienteSector|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClienteSector[]    findAll()
 * @method ClienteSector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClienteSectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClienteSector::class);
    }

    // /**
    //  * @return ClienteSector[] Returns an array of ClienteSector objects
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
    public function findOneBySomeField($value): ?ClienteSector
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
