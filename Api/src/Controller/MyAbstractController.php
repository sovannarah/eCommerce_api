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
	 * @deprecated use findUserOrFail with $admin param set to true
	 * @param Request $request
	 * @return User
	 * @throws AccessDeniedHttpException
	 * @throws UnauthorizedHttpException
	 */
	protected function _findAdminOrFail(Request $request): User
	{
		return $this->findUserOrFail($request, true);
	}

	/**
	 * @param Request $request
	 * @param bool $admin weather user should be an admin
	 * @param UserRepository $urep
	 * @return User
	 * @throws AccessDeniedHttpException
	 * @throws UnauthorizedHttpException
	 */
	protected function findUserOrFail(Request $request ,bool $admin = false): User
	{
		$token = $request->headers->get('token');
		if (!$token) {
			throw new UnauthorizedHttpException('', 'Missing Token');
		}
		$userRep = $this->getDoctrine()
			->getManager()
			->getRepository(User::class);
		// $user = $urep->findOneByToken($token);
		// dd($token);
		// dd($urep->findBy(['token' => $token])->getEmail());
		// var_dump($urep->findBy(['token' => $token]));
		$user = $admin ?
			$userRep->findAdminByToken($token) :
			$userRep->findOneByToken($token);
		if (!$user) {
			throw new AccessDeniedHttpException('Bad token');
		}
		// dd($user);
		return $user;
	}

	protected function errJson(\Exception $e): JsonResponse
	{
		$statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;

		return $this->json($e->getMessage(), $statusCode);
	}
}
