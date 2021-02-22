<?php

namespace App\Repository;

use App\Entity\Planting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Planting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planting[]    findAll()
 * @method Planting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planting::class);
    }

    // /**
    //  * @return Planting[] Returns an array of Planting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Planting
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
