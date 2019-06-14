<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function new(
        Request $request,
        ValidatorInterface $validator,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $token = $request->headers->get('token');
        $admin = $this->_findAdminOrFail($token, $userRepository);
        $article = new Article();
        try {
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

        return $this->json($article, 201);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @param Article $article
     * @return JsonResponse
     */
    public function show(Article $article): JsonResponse
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
     * @param Request $request
     * @param Article $article
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function update(
        Request $request,
        Article $article,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $admin = $this->_findAdminOrFail($userRepository, $request);
        try {
            $article->setUser($admin);
            $article->setTitle($request->request->get('title'));
            $article->setDescription($request->request->get('description'));
            $article->setPrice($request->request->get('price'));
            $this->_updateImages($article, $request->files->get('images'));
        } catch (\Exception $e) {
            $errors = $validator->validate($article);

            return $this->json($errors, 400);
        }
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->json($article, 201);
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return User
     */
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
