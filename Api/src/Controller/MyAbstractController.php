<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\{AccessDeniedHttpException,
	HttpExceptionInterface,
	UnauthorizedHttpException};

class MyAbstractController extends AbstractController
{

	/**
	 * @param Request $request
	 * @param bool $admin weather user should be an admin
	 * @return User
	 */
	protected function findUserOrFail(Request $request, bool $admin = false): User
	{
		$token = $request->headers->get('token');
		if (!$token) {
			throw new UnauthorizedHttpException('', 'Missing Token');
		}
		$userRep = $this->getDoctrine()
			->getManager()
			->getRepository(User::class);
		$user = $admin ?
			$userRep->findAdminByToken($token) :
			$userRep->findOneByToken($token);
		if (!$user) {
			throw new AccessDeniedHttpException('Bad token');
		}

		return $user;
	}

	protected function errJson(\Exception $e): JsonResponse
	{
		$statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;

		return $this->json($e->getMessage(), $statusCode);
	}
}
