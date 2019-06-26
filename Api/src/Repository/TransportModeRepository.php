<?php

namespace App\Repository;

use App\Entity\TransportMode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TransportMode|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportMode|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportMode[]    findAll()
 * @method TransportMode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportModeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TransportMode::class);
    }

    // /**
    //  * @return TransportMode[] Returns an array of TransportMode objects
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
    public function findOneBySomeField($value): ?TransportMode
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
