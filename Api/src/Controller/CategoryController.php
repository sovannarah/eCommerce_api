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
	public function		index(CategoryRepository $categories, $id)
	{
		$cat = $categories->find($id);

		$categoryArr['category'] = ['id' => $cat->getId(), 'name' => $cat->getName()];

		$categoryArr['childs'] = $this->getCategories($cat, $cat->getId());
		$categoryArr['parents'] = $this->getParentsNames($cat);
		return $this->json([
			$categoryArr
		]);
	}

	public function		getCategories($cat, $idStart) {
		$categoryArr = [];
		$child = $cat->getChildren();
		$c = -1;
		while (isset($child[++$c]))
			$categoryArr[] = $this->getCategories($child[$c], $idStart);
		if ($idStart !== $cat->getId())
			$categoryArr[] = ['id' => $cat->getId(), 'name' => $cat->getName()];
		return (array_reverse($categoryArr));
	}

	public function		getParentsNames($cat) {
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
