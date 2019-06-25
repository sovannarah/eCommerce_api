<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

abstract class AbsCheckUserController extends AbstractController
{
	/**
	 * @param Request $quest
	 * @return array
	 */
	protected function  isUser(Request $quest)
	{
		if (!($token = $quest->headers->get('token')))
			return ($this->json('no token', 404));
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findBy(['token' => $token]);
		if (!$user)
			return ($this->json('no user', 404));
		return ($this->json('found user', 200));
	}

	/**
	 * @param Request $quest
	 * @return array
	 */
	protected function isAdmin(Request $quest)
	{
		if (!($token = $quest->headers->get('token')))
			return ($this->json(['error' => 'missing token'], 401));
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findAdminByToken($token);
		if (!$user)
			return ($this->json(['error' => 'non admin'], 400));
		return ($user);
	}
}