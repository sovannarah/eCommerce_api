<?php

namespace App\Repository;

use App\Entity\SpecsOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpecsOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecsOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecsOffer[]    findAll()
 * @method SpecsOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecsOfferRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpecsOffer::class);
    }

    // /**
    //  * @return SpecsOffer[] Returns an array of SpecsOffer objects
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
    public function findOneBySomeField($value): ?SpecsOffer
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
