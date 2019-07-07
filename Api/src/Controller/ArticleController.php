<?php

namespace App\Controller;

use App\Entity\{Article, Category, VariantArticle};
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\{File\UploadedFile,
	JsonResponse,
	Request,
	Response};
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException,
	HttpExceptionInterface,
	UnauthorizedHttpException};
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/article")
 */
class ArticleController extends MyAbstractController
{
	/**
	 * @Route("", name="article_index", methods={"GET"})
	 * @param ArticleRepository $articleRepository
	 * @return Response
	 */
	public function index(ArticleRepository $articleRepository): Response
	{
		return $this->json($articleRepository->findBy([],['nb_views' => 'DESC']));
	}
	/**
	 * @Route("/{id}/variant", name="delete_variant", methods={"DELETE"})
	 */
	public function     deleteVariant(Request $quest, VariantArticle $variant)
	{
		try
		{
			$manager = $this->getDoctrine()->getManager();
			$this->findUserOrFail($quest, true);
			$manager->remove($variant);
			$manager->flush();
			return ($this->json("delete"));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("", name="article_new", methods={"POST"})
	 * @param Request $request
	 * @return Response
	 */
	public function create(Request $request): Response
	{
		$response = $this->update($request, new Article());
		if ($response->getStatusCode() === 200) {
			$response->setStatusCode(201);
		}

		return $response;
	}

	/**
	 * @Route("/{id}", name="article_show", methods={"GET"})
	 * @param Article $article
	 * @return JsonResponse
	 */
	public function read(Article $article): JsonResponse
	{
		// return $this->json([$article->getVariantArticles()[0]->getSpec(), $article->getVariantArticles()[1]->getSpec()]);
		return $this->json($article);
	}

	/**
	 * Increments views on article
	 *
	 * @Route("/{id}/increment", name="article_inc_views", methods={"PUT", "PATCH"})
	 * @param Article $article
	 * @return JsonResponse
	 */
	public function incrementViews(Article $article): JsonResponse
	{
		$article->incrementNbViews();
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($article);
		$entityManager->flush();
		$entityManager->refresh($article);

		return $this->json($article);
	}


	/**
	 * @Route("/{id}", name="article_delete", methods={"DELETE"})
	 * @param Request $request
	 * @param Article $article
	 * @return Response empty response with appropriate status code
	 */
	public function delete(Request $request, Article $article): Response
	{
		try {
			$this->findUserOrFail($request, true);
		} catch (\Exception $e) {
			$statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;

			return $this->json($e->getMessage(), $statusCode);
		}
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($article);
		$entityManager->flush();
		static::_updateImages($article, []);

		return new Response();
	}

	/**
	 * @Route("/{id}", name="article_update", methods={"POST"})
	 * @param Request $request
	 * @param Article $article
	 * @return JsonResponse
	 */
	public function update(Request $request, Article $article): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$categoryRepository = $entityManager->getRepository(Category::class);
		$category = $request->request->get('category');
		try {
			if (!isset($category['id']))
				$article->setCategory($categoryRepository->findOrFail($category));
			else
			{
				$cat = $categoryRepository->findOneBy(['id' => $category['id']]);
				$article->setCategory($cat);
			}
			$article->setUser($this->findUserOrFail($request, true))
				->setTitle($request->request->get('title'))
				->setDescription($request->request->get('description'))
				->setPrice($request->request->get('price'))
				->setStock($request->request->get('stock'));
			$this->getVariant(
				$request->request->get('variants'),
				$article, $entityManager);
			static::_updateImages($article, $request->files->get('images'));
		} catch (\Exception $e) {
			$statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;

			return $this->json($e->getMessage(), $statusCode);
		}
		$entityManager->persist($article);
		$entityManager->flush();
		$entityManager->refresh($article);

		return $this->json($article);
	}

	private function getVariant($quest, Article &$article, EntityManager $manager)
	{
		if (is_string($quest))
			$quest = json_decode($quest, true);
		$c = -1;
		$lent = count($quest);
		while (++$c < $lent)
		{
			$variant = new VariantArticle();
			$variant->setSpec($quest[$c]['spec']);
			$variant->setType($quest[$c]['type']);
			$variant->setVarPrice($quest[$c]['var_price'] * 100);
			$article->addVariantArticle($variant);
			$manager->persist($variant);
		}
	}


	/**
	 * @param Article $article
	 * @param UploadedFile[] $images
	 */
	private static function _updateImages(Article $article, $images): void
	{
		$images = (array) $images;
		foreach ($images as $image) {
			if (!getimagesize($image->getRealPath())) {
				throw new BadRequestHttpException(
					'Not an image: '.$image->getClientOriginalName()
				);
			}
		}
		$oldImages = $article->getImages();
		$article->setImages($images);
		(new Filesystem())->remove($oldImages);
	}

}
