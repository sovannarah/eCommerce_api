<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
	public function index()
	{
		return $this->json([
			'message' => 'Welcome to your new controller!',
			'path' => 'src/Controller/SearchController.php',
		]);
	}

	/**
	 * @Route("/search", name="search")
	 */
	public function     Search(Request $request, ArticleRepository $tArticle)
	{
		$gloriusQuest = $request->query->all();
		$tcheck2 = ['priceMin', 'priceMax', 'title', 'description', 'model', 'category0'];
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
			return ($this->json($this->initSearchAnd([ $gloriusQuest, $quest ],
				$tQuery, $tArticle)));
	}

	private function    initSearchAnd($quest, $tQuery ,ArticleRepository $rAtcicle)
	{
		$key = $quest[1];
		$tval = $quest[0];
		$tValCate = [];
		$n = 0;
		$c = -1;
		$lentQ = count($tQuery);
		foreach ($tval as $keys => $val)
		{
			echo ("</br>");
			if ($keys === 'category' . $n)
			{
				$tValCate[$n] = $val;
				$n++;
			}
		}
		$query = $rAtcicle->createQueryBuilder('a');
		while (++$c < $lentQ)
		{
			if (isset($tval[$tQuery[$c][0]]))
			{
				$query->andWhere($tQuery[$c][1]);
				if ($tQuery[$c][0] === 'title')
					$query->setParameter($tQuery[$c][0], $tval[$tQuery[$c][0]] . '%');
				else if ($tQuery[$c][0] === 'description')
					$query->setParameter($tQuery[$c][0], '%' . $tval[$tQuery[$c][0]] . '%');
				else
					$query->setParameter($tQuery[$c][0], $tval[$tQuery[$c][0]]);
			}
		}

//		if (isset($tval['title']))
//		{
//			$query->andWhere('a.title like :title');
//			$query->setParameter('title', $tval['title'] . '%');
//		}
		if (count($tValCate) > 0)
			$query->where($query->expr()->in('a.category', $tValCate));
//		if (isset($tval['describtion']))
//		{
//			$query->andWhere('a.description like :description');
//			$query->setParameter('description', $tval['description']. '%');
//		}
////		if (isset($val['model']))
////			$query->andWhere('a.id_model = :model';
//		if (isset($tval['priceMin']))
//		{
//			$query->andWhere('a.price > :priceMin');
//			$query->setParameter('priceMin', $tval['priceMin']);
//		}
//		if (isset($tval['priceMax']))
//		{
//			$query->andWhere('a.price < :priceMax');
//			$query->setParameter('priceMax', $tval['priceMax']);
//		}
		$art = $query->getQuery();
		$tmp = $art->execute();
		return ($tmp);
	}

	private function    func($spel)
	{

	}
}
