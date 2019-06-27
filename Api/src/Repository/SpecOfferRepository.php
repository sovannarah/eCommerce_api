<?php

namespace App\Repository;

use App\Entity\SpecOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpecOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecOffer[]    findAll()
 * @method SpecOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecOfferRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpecOffer::class);
    }

    // /**
    //  * @return SpecOffer[] Returns an array of SpecOffer objects
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
    public function findOneBySomeField($value): ?SpecOffer
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
