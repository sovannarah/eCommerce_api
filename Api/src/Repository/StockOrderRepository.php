<?php

namespace App\Repository;

use App\Entity\StockOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StockOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockOrder[]    findAll()
 * @method StockOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockOrderRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, StockOrder::class);
	}

	// /**
	//  * @return StockOrder[] Returns an array of StockOrder objects
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
	public function findOneBySomeField($value): ?StockOrder
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
