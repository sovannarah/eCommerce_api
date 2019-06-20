<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
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
	 * @Route("/{token}/check", name="is_admin", methods={"GET"})
	 * @param $token
	 * @param UserRepository $rUser
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     is_admin($token, UserRepository $rUser)
	{
		$user = $rUser->findBy(['token' => $token])[0];
		if (!$user)
			return ($this->json("bad Token", 404));
		else if (in_array('ROLE_ADMIN', $user->getRoles()) == true)
			return ($this->json(true, 200));
		return ($this->json("bad Roles", 404));
	}
}
