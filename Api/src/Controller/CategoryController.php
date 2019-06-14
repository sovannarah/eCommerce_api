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

		$categoryArr['category'] = ['id' => $cat->getId(), 'name' => $cat->getName()];

		$categoryArr['childs'] = $this->getChildrens($cat, $cat->getId());
		$categoryArr['parents'] = $this->getParents($cat);
		return $this->json([
			$categoryArr
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

	public function		getChildrens($cat, $idStart) {
		$categoryArr = [];
		$child = $cat->getChildren();
		$c = -1;
		while (isset($child[++$c]))
			$categoryArr[] = $this->getChildrens($child[$c], $idStart);
		if ($idStart !== $cat->getId())
			$categoryArr[] = ['id' => $cat->getId(), 'name' => $cat->getName()];
		return (array_reverse($categoryArr));
	}

	public function		getParents($cat) {
		$categoryArr = [];
		$parent = $cat->getParent();
		while (isset($parent))
		{
			$categoryArr[] = ['id' => $parent->getId(), 'name' => $parent->getName()];
			$parent = $parent->getParent();
		}
		return (array_reverse($categoryArr));
	}
}
