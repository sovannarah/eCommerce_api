<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, User::class);
	}

	/** @noinspection PhpDocMissingThrowsInspection */
	/**
	 * @param $token
	 * @return User|null
	 */
	public function findAdminByToken($token): ?User
	{
		try {
			return $this->createFindByTokenQB($token)
				->andWhere('u.roles LIKE :roles')
				->setParameter('roles', '%"ROLE_ADMIN"%')
				->getQuery()
				->getOneOrNullResult();
		} catch (NonUniqueResultException $e) {
			return null;
		}
	}

	public function findOneByToken($token): ?User
	{
		try {
			$res = $this->createFindByTokenQB($token)
				->getQuery()
				// ->execute();
				->getOneOrNullResult();
			return ($res);
		} catch (NonUniqueResultException $e) {
			dd($e);
			return null;
		}
	}

	private function createFindByTokenQB($token): QueryBuilder
	{
		return $this->createQueryBuilder('u')
				->where('u.token = :token')
				// ->andWhere('u.token_expiration < :now')
				->setParameter('token', $token);
				// ->setParameter('now', new \DateTime());
	}
	// /**
	//  * @return User[] Returns an array of User objects
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
	public function findOneBySomeField($value): ?User
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
