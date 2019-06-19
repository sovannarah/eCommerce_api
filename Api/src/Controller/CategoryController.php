<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
Symfony\Component\Routing\Annotation\Route,
Symfony\Component\HttpFoundation\Request,
App\Repository\UserRepository,
Symfony\Component\Validator\Validator\ValidatorInterface,
Doctrine\ORM\EntityManagerInterface,
App\Entity\Category;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{

	/**
	 * @Route("", name="all_categories", methods={"GET"})
	 *
	 * @param CategoryRepository $categoryRepository
	 * @return JsonResponse Name, id of all categoried and subcategories
	 */
	public function		readAll(CategoryRepository $categoryRepository): JsonResponse
	{
		$cats = [];
		$rootCats = $categoryRepository->findBy(['parent' => null]);
		foreach ($rootCats as $rootCat) {
			$cats[] = $rootCat->rec_nestedJsonSerialize();
		}
		return $this->json($cats);
	}

	/**
	 * @Route("/{id}", name="category", methods={"GET"})
	 *
	 * @param Category $cat
	 * @return JsonResponse requested category, parents names, subcategories and all articles of current and sub
	 */
	public function		getCategory(Category $cat): JsonResponse
	{
		return $this->json($cat);
	}

	/**
	 * @Route("", name="new_category", methods={"POST"})
	 *
	 * @param Request $req
	 * @param UserRepository $urep
	 * @param ValidatorInterface $vali
	 * @param EntityManagerInterface $manger
	 * @param CategoryRepository $categories
	 *
	 * @return JsonResponse
	 */
	public function		addCategoty(
		Request $req,
		UserRepository $urep,
		ValidatorInterface $vali,
		EntityManagerInterface $manger,
		CategoryRepository $categories
	) {
		$token = $req->headers->get('token');
		// if (!($user = $this->isAdmin($token, $urep)))
		// 	return ($resp->setStatusCode(500)->setContent("bad Request"));
		$cat = new Category();

		$cat->setName($req->request->get('name'));
		if ($req->request->get('parent'))
			$cat->setParent($categories->find($req->request->get('parent')));
		$errors = $vali->validate($cat);
		if ($errors->count())
			return ($this->json($errors, 400));
		$manger->persist($cat);
		$manger->flush();
		return ($this->json($cat->getName()));
	}

	/**
	 * @Route("/upd/{id}", name="upd_category", methods={"POST"})
	 *
	 * @param Category $cate;
	 */
	public function		updCategory(
		Category $cate,
		Request $req,
		UserRepository $urep,
		ValidatorInterface $valid,
		EntityManagerInterface $manger,
		CategoryRepository $categories,
		$id
	) {
		$token = $req->headers->get('token');
		// if (!($user = $this->isAdmin($token, $urep)))
		// 	return ($resp->setStatusCode(500)->setContent("bad Request"));

		if ($req->request->get('parent') && $req->request->get('parent') !== $cate->getId())
			$cate->setParent($categories->find($req->request->get('parent')));

		if ($req->request->get('name'))
			$cate->setName($req->request->get('name'));

		$manger->persist($cate);
		$manger->flush();

		return $this->json([$cate->getId(), $cate->getName(), $cate->getParent()->getId()]);
	}

	/**
	 * @Route("/{id}", name="del_category", methods={"DELETE"})
	 * @param Category $cat
	 * @param EntityManagerInterface $manger
	 * @return JsonResponse
	 */
	public function		deleteCategory(
		Category $cat, EntityManagerInterface $manger
	): JsonResponse {

		$manger->remove($cat);
		$manger->flush();

		return $this->json(["Deleted" => $id]);

		// return $this->json([
		// 	'category' => $categoryArr,
		// 	'articles' => $articles
		// ]);
	}


	private function	getChildrens($cat, $idStart, &$articles = null)
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
		if ($articles !== null) {
			foreach ($cat->getArticles() as $article)
				$articles[$article->getId()] = [
					'user' => [
						'id' => $article->getUser()->getId(),
						'name' => null,
						'mail' => $article->getUser()->getEmail()
					],
					'title' => $article->getTitle(),
					'description' => $article->getDescription(),
					'price' => $article->getPrice(),
					'categorie' => $article->getCategory()->getName(),
					'nb_views' => $article->getNbViews(),
					'stock' => $article->getStock()
				];
		}
		return (array_reverse($categoryArr));
	}

	private function	getParents($cat)
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

	/**
	 * @param $token
	 * @param UserRepository $uRep
	 * @return \App\Entity\User|null
	 */
	private function	isAdmin($token, UserRepository $uRep)
	{
		$user = $uRep->findOneBy(['token' => $token]);
		if (!$user || $user->isAdmin() !== true)
			return (null);
		return ($user);

	}
}
