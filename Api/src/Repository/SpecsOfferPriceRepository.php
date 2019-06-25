<?php

namespace App\Repository;

use App\Entity\SpecsOfferPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpecsOfferPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecsOfferPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecsOfferPrice[]    findAll()
 * @method SpecsOfferPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecsOfferPriceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpecsOfferPrice::class);
    }

    // /**
    //  * @return SpecsOfferPrice[] Returns an array of SpecsOfferPrice objects
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
    public function findOneBySomeField($value): ?SpecsOfferPrice
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
