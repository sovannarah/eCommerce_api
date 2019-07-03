<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends MyAbstractController
{
	/**
	 * @Route("/", name="user", methods={"GET"})
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function index(Request $request)
	{
		$user = ($this->findUserOrFail($request));
		/* Uncomment next line to debug on Postman */
		// self::showUserOnPM($user);

		return $this->json($user);
	}

	/**
	 * @Route("/{token}/check", name="is_admin", methods={"GET"})
	 * @param $token
	 * @param UserRepository $rUser
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     is_admin($token, UserRepository $rUser)
	{
		// $user = $rUser->findBy(['token' => $token])[0];
		$user = $rUser->findBy(['token' => $token]);
		if(count($user) < 1)
			return ($this->json("bad Token", 403));
		$user = $user[0];
		if (!$user)
			return ($this->json("bad Token", 403));
		else if (in_array('ROLE_ADMIN', $user->getRoles()) == true)
			return ($this->json(true, 200));
		return ($this->json("bad Roles", 403));
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
		} catch (UnauthorizedHttpException | AccessDeniedHttpException $e) {
			return $this->json($e->getMessage(), $e->getStatusCode());
		}
	}

	/**
	 * Debug function to view user informations and orders on Postman
	 */
	private function showUserOnPM($user)
	{
		echo("===== USER =====<br>Email: ".$user->getEmail()."<br>Roles:<br>");
		foreach ($user->getRoles() as $value)
			echo "- $value<br>";
		echo("===== ORDERS =====<br>");
		foreach($user->getUserOrders() as $key => $value) //get orders one by one
		{
			echo "=> Commande du: "
				.$value->getSend()->format('d/m/Y H:i:s')."<br>";
			foreach($value->getOrderItems() as $key => $value) //get item one by one
				echo "-> ".$value->getArticle()->getTitle().": "
					.$value->getQuantity()."<br>";
		}
		die();
	}
}
