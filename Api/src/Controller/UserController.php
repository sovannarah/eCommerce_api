<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends MyAbstractController
{
	/**
	 * Send user email and role if user exists
	 * 
	 * @Route("/", name="user", methods={"GET"})
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function index(Request $request)
	{
		$user = ($this->findUserOrFail($request));
		/* Uncomment next line to debug on Postman */
		// self::showUserOnPM($user);
		return $this->json(self::_jsonUser($user));
	}

	/**
	 * Update user informations
	 * 
	 * @Route("", name="upd_user", methods={"POST"})
	 * @param Request $request
	 * @throws BadRequestHttpException
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function upd_user(Request $request)
	{
		$user = ($this->findUserOrFail($request));
		/* Uncomment next line to debug on Postman */
		// self::showUserOnPM($user);

		try {
			foreach($request->request->all() as $key => $value) {
				$fn = "set".ucfirst($key);
				$user->$fn($value);
				// echo("set".ucfirst($key)."(".$value.")<br>");
			}
		} catch (\Throwable $e) {
			$statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;
			// throw new BadRequestHttpException($e->getMessage());
			return $this->json($e->getMessage(), $statusCode);
		}

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user);
		$entityManager->flush();

		return $this->json($user);
	}

	/**
	 * Check if user is admin
	 * 
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
	 * Check if user is logged
	 * 
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

	private function _jsonUser($user){
		return [
			'email' => $user->getEmail(),
			'roles' => $user->getRoles(),
		];
	}
}
