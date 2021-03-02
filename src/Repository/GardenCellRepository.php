<?php

namespace App\Repository;

use App\Entity\GardenCell;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GardenCell|null find($id, $lockMode = null, $lockVersion = null)
 * @method GardenCell|null findOneBy(array $criteria, array $orderBy = null)
 * @method GardenCell[]    findAll()
 * @method GardenCell[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GardenCellRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GardenCell::class);
    }

    // /**
    //  * @return GardenCell[] Returns an array of GardenCell objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GardenCell
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
