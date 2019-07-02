<?php

namespace App\Repository;

use App\Entity\SpecOffer;
use App\Entity\TransportMode;
use App\Entity\TransportOffer;
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
	public function getAll()
	{
		$tTransport = $this->findAll();
		$c = -1;
		$lenTr = count($tTransport);
		$resp = [];
		while (++$c < $lenTr)
		{
			$resp[$c] = [
				'id' => $tTransport[$c]->getId(),
				'name' => $tTransport[$c]->getName(),
				'offers' => $this->getOffer($tTransport[$c]->getTransportOffers())];
		}
		return ($resp);
	}
	public function getOffer($Roffer)
	{
		$len = count($Roffer);
		$c = -1;
		$offerResp = [];
		while (++$c < $len)
		{
			$offerResp[$c] = [
				"id" => $Roffer[$c]->getId(),
				'name' =>$Roffer[$c]->getName(),
				'specs' => $this->getSpec($Roffer[$c]->getSpecOffers())
			];
		}
		return ($offerResp);
	}
	public function getSpec($soffer)
	{
		$len = count($soffer);
		$c = -1;
		$specs = [];
		while (++$c < $len)
		{
			$specs[$c] = [
				'id' => $soffer[$c]->getId(),
				'name' => $soffer[$c]->getName(),
				'unity' => $soffer[$c]->getUnity(),
				'minValue' => $soffer[$c]->getMinValue(),
				'price' => $soffer[$c]->getPrice()
			];
		}
		return ($specs);
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
