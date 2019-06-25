<?php

namespace App\Repository;

use App\Entity\StockOrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StockOrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockOrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockOrderItem[]    findAll()
 * @method StockOrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockOrderItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StockOrderItem::class);
    }

    // /**
    //  * @return StockOrderItem[] Returns an array of StockOrderItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StockOrderItem
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
