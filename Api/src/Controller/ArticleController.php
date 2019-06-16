<?php

namespace App\Controller;

use App\Entity\{Article, Category, User};
use App\Repository\ArticleRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{File\UploadedFile, JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
	/**
	 * @Route("/", name="article_index", methods={"GET"})
	 * @param ArticleRepository $articleRepository
	 * @return Response
	 */
	public function index(ArticleRepository $articleRepository): Response
	{
		return $this->json($articleRepository->findAll());
	}

	/**
	 * @Route("/", name="article_new", methods={"POST"})
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function create(Request $request): JsonResponse
	{
		return $this->update($request, new Article())->setStatusCode(201);
	}

	/**
	 * @Route("/{id}", name="article_show", methods={"GET"})
	 * @param Article $article
	 * @return JsonResponse
	 */
	public function read(Article $article): JsonResponse
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
	 * @return Response
	 */
	public function delete(Request $request, Article $article): Response
	{
		$this->_findAdminOrFail($request);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($article);
		$entityManager->flush();
		self::_updateImages($article, []);

		return new Response();
	}

	/**
	 * @Route("/{id}", name="article_update", methods={"PUT", "PATCH"})
	 * @Route("/{id}/update", methods={"POST"})
	 * @param Request $request
	 * @param Article $article
	 * @return JsonResponse
	 */
	public function update(Request $request, Article $article): JsonResponse
	{
		$entityManager = $this->getDoctrine()->getManager();
		$admin = $this->_findAdminOrFail($request);
		$categoryRepository = $entityManager->getRepository(Category::class);
		$category = $categoryRepository->findOrFail($request->request->get('category'));
		try {
			$article->setCategory($category);
			$article->setUser($admin);
			$article->setTitle($request->request->get('title'));
			$article->setDescription($request->request->get('description'));
			$article->setPrice($request->request->get('price'));
			$article->setStock($request->request->get('stock'));
			self::_updateImages($article, $request->files->get('images'));
		} catch (\Exception $e) {
			throw new InvalidParameterException('', 400, $e);
		}
		$entityManager->persist($article);
		$entityManager->flush();
		$entityManager->refresh($article);

		return $this->json($article);
	}


	/**
	 * @param Request $request
	 * @return User
	 * @throws AccessDeniedException
	 */
	private function _findAdminOrFail(Request $request): User
	{
		$token = $request->headers->get('token');
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findAdminByToken($token);
		if (!$user) {
			throw $this->createAccessDeniedException();
		}

		return $user;
	}

	/**
	 * @param Article $article
	 * @param UploadedFile[] $images
	 */
	private static function _updateImages(Article $article, array $images): void
	{
		foreach ($images as $image) {
			if (!getimagesize($image->getRealPath())) {
				throw new InvalidParameterException(
					'Not an image: '.$image->getClientOriginalName()
				);
			}
		}
		$oldImages = $article->getImages();
		$article->setImages($images);
		(new Filesystem())->remove($oldImages);
	}

}
