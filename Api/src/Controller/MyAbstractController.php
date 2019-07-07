<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\{
	AccessDeniedHttpException,
	HttpExceptionInterface,
	NotFoundHttpException,
	UnauthorizedHttpException};

class MyAbstractController extends AbstractController
{

	/**
	 * @param Request $request
	 * @param bool $admin weather user should be an admin
	 * @return User
	 * @throws UnauthorizedHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function findUserOrFail(Request $request, bool $admin = false): User
	{
		try {
			$user = $this->tryFindUser($request, $admin);
		} catch (NotFoundHttpException $e) {
			throw new AccessDeniedHttpException('Bad token', $e);
		}
		if (!$user) {
			throw new UnauthorizedHttpException('', 'Missing Token');
		}

		return $user;
	}

	/**
	 * @param Request $request
	 * @param bool $admin
	 * @return User|null
	 * @throws NotFoundHttpException if token is given but no user found
	 */
	protected function tryFindUser(Request $request, bool $admin = false): ?User
	{
		$token = $request->headers->get('token');
		if (!$token) {
			return null;
		}
		$userRep = $this->getDoctrine()
			->getManager()
			->getRepository(User::class);
		$user = $admin ?
			$userRep->findAdminByToken($token) :
			$userRep->findOneByToken($token);
		if (!$user) {
			throw new NotFoundHttpException("Couldn't find User by given token");
		}

		return $user;
	}

	protected function errJson(\Exception $e): JsonResponse
	{
		$statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;

		return $this->json($e->getMessage(), $statusCode);
	}


	/**
	 * Shortcut function to filter $var as "natural int" (>=0).
	 *
	 * 1.0 passes, 1.1 does not
	 *
	 * @param mixed $var passed by reference to allow undeclared
	 * @return int|null value or null on failure
	 */
	public static function filterNaturalInt(&$var): ?int
	{
		return
			$var === null ?
			null :
			filter_var(
				$var,
				FILTER_VALIDATE_INT,
				[
					'options' => [
						'default' => null,
						'min_range' => 0,
					],
				]
			);
	}
}
