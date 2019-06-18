<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, Category::class);
	}

	/**
	 * Tries to find category by $id, throwing exception on failure
	 *
	 * @param $id
	 * @param null $lockMode
	 * @param null $lockVersion
	 * @return Category
	 * @throws NotFoundHttpException on failure
	 */
	public function findOrFail($id = null, $lockMode = null, $lockVersion = null): Category
	{
		$category = $id !== null ? $this->find($id, $lockMode, $lockVersion) : null;
		if ($category === null) {
			throw new NotFoundHttpException(
				"Unable to find {$this->getClassName()} with id: {$id}"
			);
		}

		return $category;
	}

	// /**
	//  * @return Category[] Returns an array of Category objects
	//  */
	/*
	public function findByExampleField($value)
	{
		return $this->createQueryBuilder('c')
			->andWhere('c.exampleField = :val')
			->setParameter('val', $value)
			->orderBy('c.id', 'ASC')
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	*/

	/*
	public function findOneBySomeField($value): ?Category
	{
		return $this->createQueryBuilder('c')
			->andWhere('c.exampleField = :val')
			->setParameter('val', $value)
			->getQuery()
			->getOneOrNullResult()
		;
	}
	*/
}
