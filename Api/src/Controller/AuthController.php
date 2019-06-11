<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\SerializerBundle\JMSSerializerBundle;


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
	 * Extract email and password from POST request
	 *  Manually check if not null
	 *  
	 * @Route("/register", name="api_register")
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
	{
		$user = new User();

		// method point after point
		if(!$request->request->get('email')) {
			return $this->json([
				'errors' => "email is required",
			]);
		}
		if(!$request->request->get('password')) {
			return $this->json([
				'errors' => "password is required",
			]);
		}

		// method try catch
		/* try {
			$user->setEmail($request->request->get('email'));
			$user->setPassword($request->request->get('password'));
		} catch (\Throwable $e) {
			$errors = $validator->validate($user);
			return $this->json(['errors' => $errors, 'e' => $e]);
		} */

		// $data = $this->get('jms_serializer')->serialize($user, 'json');

		$user->setEmail($request->request->get('email'));
		$plainPassword = $request->request->get('password');
		$encoded = $passwordEncoder->encodePassword($user, $plainPassword);
		$user->setPassword($encoded);

		$errors = $validator->validate($user);

		if(count($errors) > 0) {
			return $this->json(['errors' => $errors]);
		} else {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();
			return $this->json([
				'email' => $request->request->get('email'),
				'user_id' => $user->getId()
			]);
		}

		/* return new Response('<pre>'.print_r($request->request->all()).'</pre>'); */
		/* return $this->json([
			'email' => $request->request->get('email'),
			'password' => $request->request->get('password'),
		]); */
	}
}
