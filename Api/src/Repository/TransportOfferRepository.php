<?php

namespace App\Repository;

use App\Entity\TransportOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TransportOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportOffer[]    findAll()
 * @method TransportOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportOfferRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TransportOffer::class);
    }

    // /**
    //  * @return TransportOffer[] Returns an array of TransportOffer objects
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
    public function findOneBySomeField($value): ?TransportOffer
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
