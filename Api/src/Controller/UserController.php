<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\{AccessDeniedHttpException,
	BadRequestHttpException,
	HttpException,
	NotFoundHttpException,
	UnauthorizedHttpException};
/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends MyAbstractController
{
	/**
	 * @Route("/user", name="user")
	 */
	public function index()
	{
		return $this->json([
			'message' => 'Welcome to your new controller!',
			'path' => 'src/Controller/UserController.php',
		]);
	}

	/**
	 * @Route("/isAdmin", name="is_admin", methods={"GET"})
	 * @param UserRepository $rUser
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     is_admin(Request $req)
	{
		try
		{
			return ($this->json($this->findUserOrFail($req, true)));
		} catch (UnauthorizedHttpException | AccessDeniedHttpException $e)
		{
			return($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("/checkuser", name="is_user", methods={"GET"})
	 * @param UserRepository $rUser
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	// public function     is_user($token, UserRepository $rUser, Request $request)
	public function     is_user(UserRepository $rUser, Request $request)
	{
		try {
			return $this->json(
				$this->findUserOrFail($request)->getEmail()
			);
		} catch (\Exception $e) {

			return $this->json($e->getMessage(), $e->getStatusCode());
		}
	}
}
