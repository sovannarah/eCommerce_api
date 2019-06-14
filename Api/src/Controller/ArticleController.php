<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function create(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $admin = $this->_findAdminOrFail($entityManager->getRepository(User::class), $request);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->find($request->request->get('category_id'));
        $article = new Article();
        try {
            $article->setCategory($category);
            $article->setUser($admin);
            $article->setTitle($request->request->get('title'));
            $article->setDescription($request->request->get('description'));
            $article->setPrice($request->request->get('price'));
            $article->setImages($request->files->get('images'));
        } catch (\Exception $e) {
            $errors = $validator->validate($article);

            return $this->json($errors, 400);
        }
        $entityManager->persist($article);
        $entityManager->flush();
        $entityManager->refresh($article);

        return $this->json($article, 201);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @param Article $article
     * @return JsonResponse
     */
    public function read(Article $article): JsonResponse
    {
        return $this->json($article);
    }


    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @param Request $request
     * @param Article $article
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(
        Request $request,
        Article $article,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->_findAdminOrFail($userRepository, $request);
        $entityManager->remove($article);
        $entityManager->flush();
        $this->_updateImages($article, []);

        return new Response();
    }

    /**
     * @Route("/{id}", name="article_update", methods={"PUT", "PATCH"})
     * @Route("/{id}/update", methods={"POST"})
     * @param Request $request
     * @param Article $article
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function update(
        Request $request,
        Article $article,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $admin = $this->_findAdminOrFail($entityManager->getRepository(User::class), $request);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->find($request->request->get('category_id'));
        try {
            $article->setCategory($category);
            $article->setUser($admin);
            $article->setTitle($request->request->get('title'));
            $article->setDescription($request->request->get('description'));
            $article->setPrice($request->request->get('price'));
            $article->setImages($request->files->get('images'));
        } catch (\Exception $e) {
            throw new InvalidParameterException('', 400, $e);
        }
        $entityManager->persist($article);
        $entityManager->flush();
        $entityManager->refresh($article);

        return $this->json($article);
    }

    private function _findAdminOrFail(UserRepository $userRepository, Request $request): User
    {
        $token = $request->headers->get('token');
        $user = $userRepository->findAdminByToken($token);
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

    private function _updateImages(Article $article, array $images): void
    {
        $oldImages = $article->getImages();
        $article->setImages($images);
        (new Filesystem())->remove($oldImages);
    }
}
