<?php

namespace App\Repository;

use App\Entity\GardenCell;
use App\Entity\History;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    /**
     * @param GardenCell[] $cell
     * @param int $year
     * @return History[]
     */
    public function findByCellAndYear(array $cell, int $year): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.cell in (:cell)')
            ->andWhere('h.plantedFrom >= :year_start')
            ->andWhere('h.plantedFrom <= :year_end')
            ->andWhere('h.plantedTo <= :year_end OR h.plantedTo IS NULL')
            ->setParameter('cell', $cell)
            ->setParameter('year_start', (new Datetime)->setDate($year,1,1))
            ->setParameter('year_end', (new Datetime)->setDate($year,12,31))
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?History
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
