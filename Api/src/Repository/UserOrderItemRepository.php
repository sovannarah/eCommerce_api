<?php

namespace App\Repository;

use App\Entity\UserOrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserOrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserOrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserOrderItem[]    findAll()
 * @method UserOrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserOrderItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserOrderItem::class);
    }

    // /**
    //  * @return UserOrderItem[] Returns an array of UserOrderItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserOrderItem
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
