<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController,
	Symfony\Component\Routing\Annotation\Route,
	Symfony\Component\HttpFoundation\Request,
	Doctrine\ORM\EntityManagerInterface,
	App\Entity\Category;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

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
	 * @param ArticleRepository $articles
	 * @return JsonResponse
	 */
	public function readNestedArticles(Category $category, ArticleRepository $articles): JsonResponse
	{
		$categoryIds= [];
		$category->getDeepChildrenId($categoryIds);
		$articles->findBy(['category' => $categoryIds]);
		return $this->json(
			$category->getArticles()->map(
				static function (Article $article) {
					return $article->nestedJsonSerialize();
				}
			)->toArray()
		);
	}

	/**
	 * @Route("", name="category_create", methods={"POST"})
	 *
	 * @param Request $req
	 * @param EntityManagerInterface $manger
	 * @return JsonResponse
	 */
	public function create(Request $req, EntityManagerInterface $manger): JsonResponse
	{
		$res = $this->update(new Category(), $req, $manger);
		if ($res->getStatusCode() === 200) {
			return $res->setStatusCode(200);
		}
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
		$admin = $manger->getRepository(User::class)
			->findAdminByToken($req->headers->get('token'));
		if (!$admin) {
			return $this->json('invalid/missing token', 401);
		}
		try {
			$this->_setParentOn($cat, $req->request->get('parentId'));
		} catch (InvalidParameterException | NotFoundHttpException $e) {
			$status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;
			return $this->json($e->getMessage(), $status);
		}
		$name = $req->request->get('name');
		if (!$name) {
			return $this->json('invalid name', 400);
		}
		$cat->setName($name);
		$manger->persist($cat);
		$manger->flush();
		$manger->refresh($cat);

		return $this->json($cat);
	}

	/**
	 * @param Category $category
	 * @param $parentId
	 * @throws InvalidParameterException | NotFoundHttpException
	 */
	private function _setParentOn(Category $category, $parentId): void
	{
		if ($parentId === null || $parentId === '') {
			$category->setParent(null);

			return;
		}
		if ($category->getId() === $parentId) {
			throw new InvalidParameterException('Parent Category cannot be self');
		}
		$parent = $this->getDoctrine()->getManager()
			->getRepository(Category::class)
			->find($parentId);
		if (!$parent) {
			throw new NotFoundHttpException("No Category with id: $parentId");
		}
		if ($category->isDeepParentOf($parent)) {
			throw new InvalidParameterException('Circular hierarchy: parent category is a child');
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
