<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
	Symfony\Component\Routing\Annotation\Route,
	Symfony\Component\HttpFoundation\Request,
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
	 * @Route("", name="categories_all", methods={"GET"})
	 *
	 * @param CategoryRepository $categoryRepository
	 * @return JsonResponse Name, id, and subcategories of all categories (recursively),
	 *   starting from the root categories
	 */
	public function readAll(CategoryRepository $categoryRepository): JsonResponse
	{
		$cats = [];
		$rootCats = $categoryRepository->findBy(['parent' => null]);
		foreach ($rootCats as $rootCat) {
			$cats[] = $rootCat->rec_nestedJsonSerialize();
		}

		return $this->json($cats);
	}

	/**
	 * @Route("/{id}", name="category_read", methods={"GET"})
	 *
	 * @param Category $cat
	 * @return JsonResponse requested category, parents names, subcategories and all articles of current and sub
	 */
	public function read(Category $cat): JsonResponse
	{
		return $this->json($cat);
	}

	/**
	 * @Route("/{id}/article", name="category_article_all")
	 * @param Category $category
	 * @return JsonResponse
	 */
	public function readNestedArticles(Category $category): JsonResponse
	{
		return $this->json($category->getArticles()->toArray());
	}

	/**
	 * @Route("", name="category_create", methods={"POST"})
	 *
	 * @param Request $req
	 * @param EntityManagerInterface $manger
	 * @return JsonResponse
	 */
	public function create(Request $req, EntityManagerInterface $manger): JsonResponse {
		return $this->update(new Category(), $req, $manger)->setStatusCode(201);
	}

	/**
	 * @Route("/{id}", name="category_upd", methods={"POST"})
	 *
	 * @param Category $cat ;
	 * @param Request $req
	 * @param EntityManagerInterface $manger
	 * @return JsonResponse
	 */
	public function update(
		Category $cat,
		Request $req,
		EntityManagerInterface $manger
	): JsonResponse {
		$token = $req->headers->get('token');
		$admin = $manger->getRepository(User::class)->findAdminByToken($token);
		if (!$admin) {
			return $this->json('invalid/missing token', 401);
		}
		$this->_setParent($cat, $req->request->get('parentId'));
		$name = $req->request->get('name');
		if ($name) {
			$cat->setName($name);
		}
		$manger->persist($cat);
		$manger->flush();
		$manger->refresh($cat);

		return $this->json($cat);
	}

	private function _setParent(Category $category, $parentId): void
	{
		$parent = $this->getDoctrine()->getManager()
			->getRepository(Category::class)
			->find($parentId);
		if ($parentId !== null && !$parent) {
			throw new InvalidParameterException("No parent found with id: $parentId");
		}
		$category->setParent($parent);
	}

	/**
	 * @Route("/{id}", name="del_category", methods={"DELETE"})
	 * @param Request $request
	 * @param Category $cat
	 * @param EntityManagerInterface $manger
	 * @return JsonResponse
	 */
	public function deleteCategory(
		Request $request,
		Category $cat,
		EntityManagerInterface $manger
	): JsonResponse {
		$token = $request->headers->get('token');
		if (!$token || !$manger->getRepository(User::class)
				->findAdminByToken($token)) {
			return $this->json('invalid token', 401);
		}
		$manger->remove($cat);
		$manger->flush();

		return $this->json(['Deleted' => $cat->getId()]);
	}
}
