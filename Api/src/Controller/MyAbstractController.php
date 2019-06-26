<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\{AccessDeniedHttpException, UnauthorizedHttpException};

class MyAbstractController extends AbstractController
{

	/**
	 * @param Request $request
	 * @return User
	 * @throws AccessDeniedHttpException
	 * @throws UnauthorizedHttpException
	 */
	protected function _findAdminOrFail(Request $request): User
	{
		$token = $request->headers->get('token');
		if (!$token) {
			throw new UnauthorizedHttpException('', 'Missing Token');
		}
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findAdminByToken($token);
		if (!$user) {
			throw new AccessDeniedHttpException();
		}

		return $user;
	}
}
