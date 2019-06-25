<?php

namespace App\Repository;

use App\Entity\VariantArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VariantArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method VariantArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method VariantArticle[]    findAll()
 * @method VariantArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariantArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VariantArticle::class);
    }

    // /**
    //  * @return VariantArticle[] Returns an array of VariantArticle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VariantArticle
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
