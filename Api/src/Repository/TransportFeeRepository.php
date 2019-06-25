<?php

namespace App\Repository;

use App\Entity\TransportFee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TransportFee|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportFee|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportFee[]    findAll()
 * @method TransportFee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportFeeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TransportFee::class);
    }

    // /**
    //  * @return TransportFee[] Returns an array of TransportFee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TransportFee
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
