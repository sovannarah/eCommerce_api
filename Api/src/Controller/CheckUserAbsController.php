<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

abstract class CheckUserAbsController extends AbstractController
{
	protected function isAdmin(Request $quest)
	{
		if (!($token = $quest->headers->get('token')))
			return ([$this->json(['error' => 'missing token'], 404)]);
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findAdminByToken($token);
		if (!$user)
			return ([$this->json(['error' => 'non admin'], 400)]);
		return ($user);
	}
}