<?php

namespace App\Controller;

use App\Entity\{Article, Category};
use App\Repository\ArticleRepository;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{File\UploadedFile,
	JsonResponse,
	Request,
	Response};
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, HttpExceptionInterface};
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
			$this->_findAdminOrFail($request);
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
			$admin = $this->_findAdminOrFail($request);
			$category = $categoryRepository->findOrFail($category);
			$article->setCategory($category)
				->setUser($admin)
				->setTitle($request->request->get('title'))
				->setDescription($request->request->get('description'))
				->setPrice($request->request->get('price'))
				->setStock($request->request->get('stock'));
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


	/**
	 * @param Article $article
	 * @param UploadedFile[] $images
	 */
	private static function _updateImages(Article $article, array $images = null): void
	{
		$images = $images ?? [];
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
