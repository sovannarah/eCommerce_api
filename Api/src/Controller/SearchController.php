<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{

	/**
	 * @Route("/search", name="search", methods={"GET"})
	 * @param Request $request
	 * @param ArticleRepository $tArticle
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     Search(Request $request, ArticleRepository $tArticle)
	{
		$gloriusQuest = $request->query->all();
		$tcheck2 = ['priceMin', 'priceMax', 'title', 'description', 'model', 'category'];
		$tQuery = [
			['priceMin', 'a.price > :priceMin'],
			['priceMax', 'a.price < :priceMax'],['title', 'a.title like :title'],
			['description', 'a.description like :description'], ['model', '']];
		$findValue = false;
		foreach ($gloriusQuest as $quest => $value)
		{
			if (in_array($quest, $tcheck2) === true)
			{
				$findValue = true;
				break;
			}
		}
		if ($findValue !== true)
			return ($this->json($tArticle->findAll()));
		else
			return ($this->json($this->initSearchAnd($gloriusQuest,
				$tQuery, $tArticle)));
	}

	/**
	 * @param $quest
	 * @param $tQuery
	 * @param ArticleRepository $rAtcicle
	 * @return mixed
	 */
	private function    initSearchAnd($quest, $tQuery ,ArticleRepository $rAtcicle)
	{
		$c = -1;
		$lentQ = count($tQuery);
		$query = $rAtcicle->createQueryBuilder('a');
		while (++$c < $lentQ)
		{
			if (isset($quest[$tQuery[$c][0]]))
			{
				$query->andWhere($tQuery[$c][1]);
				if ($tQuery[$c][0] === 'title')
					$query->setParameter($tQuery[$c][0], $quest[$tQuery[$c][0]] . '%');
				else if ($tQuery[$c][0] === 'description')
					$query->setParameter($tQuery[$c][0], '%' . $quest[$tQuery[$c][0]] . '%');
				else
					$query->setParameter($tQuery[$c][0], $quest[$tQuery[$c][0]]);
			}
		}
		if (isset($quest['category']) && count($quest['category']) > 0)
			$query->andWhere($query->expr()->in('a.category', $quest['category']));
		$art = $query->getQuery()->execute();
		return ($art);
	}
}
