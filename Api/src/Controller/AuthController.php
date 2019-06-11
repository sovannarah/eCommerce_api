<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AuthController extends AbstractController
{
	/**
	 * @Route("/login", name="api_login")
	 */
	public function login()
	{
		return $this->json([
			'message' => 'Welcome to your new controller!',
			'path' => 'src/Controller/AuthController.php',
		]);
	}

	/**
	 * @Route("/register", name="api_register")
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
	{
		$user = new User();

		
		$user->setEmail($request->request->get('email'));
		$user->setPassword($request->request->get('password'));
		
		// method point after point
		/* if(!$request->request->get('email')) {
			return $this->json([
					'email' => $request->request->get('email'),
					'password' => $request->request->get('password'),
				]);
		} */

		// method try catch
		/* try {
			$user->setEmail($request->request->get('email'));
			$user->setPassword($request->request->get('password'));
		} catch (\Throwable $e) {
			$errors = $validator->validate($user);
			return $this->json(['errors' => $errors, 'e' => $e]);
		} */

		$data = $this->get('jms_serializer')->serialize($user, 'json');

		$errors = $validator->validate($user);

		return new Response(
			'<html><body><pre>'.print_r($request->request->all()).'</pre></body></html>'
		);

		/* if(count($errors) > 0) {
			return $this->json(['errors' => $errors]);
		} else {
			return $this->json([
				'email' => $request->request->get('email'),
				'password' => $request->request->get('password'),
			]);
		} */

		/* return new Response(
		    '<html><body><pre>'.print_r($request->request->all()).'</pre></body></html>'
		); */
		/* return $this->json([
			'email' => $request->request->get('email'),
			'password' => $request->request->get('password'),
		]); */
	}
}
