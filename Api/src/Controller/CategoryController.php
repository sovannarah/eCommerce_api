<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
	/**
	 * @Route("/category/{id}", name="category")
	 */
	public function		getCategory(CategoryRepository $categories, $id)
	{
		$cat = $categories->find($id);

		$articles = [];
		$categoryArr = [
			'category' => ['id' => $cat->getId(), 'name' => $cat->getName()],
			'childs' => $this->getChildrens($cat, $cat->getId(), $articles),
			'parents' => $this->getParents($cat)
		];

		return $this->json([
			'category' => $categoryArr,
			'articles' => $articles
		]);
	}
	
	/**
	 * @Route("/categories/", name="all_categories")
	 */
	public function		getCategories(CategoryRepository $categories)
	{
		$cats = [];
		$cat = $categories->findBy(['parent' => null]);

		foreach ($cat as $key => $value) {
			$cats[$value->getId()] = [
				'name' => $value->getName(),
				'sub' => $this->getChildrens($value, $value->getId())
			];
		}

		// $categoryArr['category'] = ['id' => $cat->getId(), 'name' => $cat->getName()];

		// $categoryArr['childs'] = $this->getChildrens($cat, $cat->getId());
		// $categoryArr['parents'] = $this->getParents($cat);
		return $this->json([
			$cats
		]);
	}

	public function		getChildrens($cat, $idStart, &$articles = null)
	{
		$categoryArr = [];
		$child = $cat->getChildren();
		$c = -1;
		$tableLen = count($child);
		while (++$c < $tableLen)
			$categoryArr[] = $this->getChildrens($child[$c], $idStart,
				$articles);
		if ($idStart !== $cat->getId()) 
			$categoryArr[] = ['id' => $cat->getId(), 'name' => $cat->getName()];
		if ($articles) {
			foreach ($cat->getArticles() as $article)
			$articles[] = $article->getTitle();
		}
		return (array_reverse($categoryArr));
	}

	public function		getParents($cat)
	{
		$categoryArr = [];
		$parent = $cat->getParent();
		while (isset($parent))
		{
			$categoryArr[] = ['id' => $parent->getId(),
				'name' => $parent->getName()];
			$parent = $parent->getParent();
		}
		return (array_reverse($categoryArr));
	}
}
