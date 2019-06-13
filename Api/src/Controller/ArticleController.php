<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/new", name="article_new", methods={"POST"})
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
        $token = $request->headers->get('token');
        $this->_findAdminOrFail($token, $userRepository);
        $entityManager->remove($article);
        $entityManager->flush();

        return new Response();
    }

    /**
     * @Route("/{id}/update", name="article_update", methods={"PUT", "PATCH"})
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
        $token = $request->headers->get('token');
        $admin = $this->_findAdminOrFail($token, $userRepository);
        try {
            $article->setUser($admin);
            foreach (['title', 'description', 'price'] as $fieldName) {
                $article->{'set' . $fieldName}($request->request->get('title'));
            }
            $article->setImages($request->files->get('images'));
        } catch (\Exception $e) {
            $errors = $validator->validate($article);

            return $this->json($errors, 400);
        }
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->json($article, 201);
    }

    private function _updateImages(Article $article, array $images)
    {

    }


    /**
     * @param string $token
     * @param UserRepository $userRepository
     * @return User
     * @throws AccessDeniedException
     */
    private function _findAdminOrFail(string $token, UserRepository $userRepository): User
    {
        $user = $userRepository->findOneByToken($token);
        if (!$user || !$user->isAdmin()) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

}



/*/*
 * @param string $userId
 * @param UploadedFile[] $uploadedFiles
 * @return array
 *//*
    private function _saveImages(string $userId, array $uploadedFiles): array
    {
        $newFiles = [];
        foreach ($uploadedFiles as $file) {
            $fileName = md5(uniqid($userId)).'.'.$file->guessExtension();
            try {
                $dir = $this->getParameter('images_directory');
                $newFiles[] = $file->move($dir, $fileName);
            } catch (FileException $e) {
                // TODO ... handle exception if something happens during file upload
            }
        }

        return $newFiles;
    }*/
