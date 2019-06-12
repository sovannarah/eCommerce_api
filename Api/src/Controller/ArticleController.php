<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Exception\InvalidParameterException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/new", name="Article_new", methods={"POST"})
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
        $user = $userRepository->findOneByToken($request->headers->get('token'));
        if (!$user || !$user->isAdmin()) {
            throw $this->createAccessDeniedException();
        }
        $article = new Article();
        try {
            $article->setUser($user);
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
        return $this->json($article->getId(), 201);
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
