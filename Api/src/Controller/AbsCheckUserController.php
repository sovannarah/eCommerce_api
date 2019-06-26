<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbsCheckUserController extends AbstractController
{
	/**
	 * @param Request $quest
	 * @return User|JsonResponse
	 */
	protected function  isUser(Request $quest)
	{
		if (!($token = $quest->headers->get('token')))
			return ($this->json('no token', 400));
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findOneBy(['token' => $token]);
		if (!$user)
			return ($this->json('no user', 404));
		return ($user);
	}

	protected function isAdmin(Request $quest)
	{
		if (!($token = $quest->headers->get('token')))
			return ($this->json('missing token', 401));
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findAdminByToken($token);
		if (!$user)
			return ($this->json('non admin', 400));
		return ($user);
	}
}
