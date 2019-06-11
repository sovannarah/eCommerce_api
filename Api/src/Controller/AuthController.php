<?php

namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\Routing\Annotation\Route;

// use App\Entity\User;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Symfony\Component\Validator\Validator\ValidatorInterface;
// use JMS\SerializerBundle\JMSSerializerBundle;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \JMS\Serializer\SerializationContext;
use \JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Component\Validator\ConstraintViolationList;


class AuthController extends FOSRestController
{
	/**
	 * @Rest\Post("/login", name="api_login")
	 */
	public function login()
	{
		return $this->json([
			'message' => 'Welcome to your new controller!',
			'path' => 'src/Controller/AuthController.php',
		]);
	}

	/**
	 * Register a new user
	 *  Extract email and password from POST request
	 *  Manually check if not null
	 *  Automatically validate the data according to ORM entity
	 *  Encode password
	 *  Stores user in DB
	 * 
	 * @Rest\Post("/register", name="api_register")
	 * @Rest\View
	 * @ParamConverter("user", converter="fos_rest.request_body")
	 */
	// public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
	public function register(User $user, ConstraintViolationList $violations, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		// $userEmail = $request->request->get('email');
		// $plainPassword = $request->request->get('password');
		/* Check if email or password are null */
		// if(!$userEmail || !$plainPassword)
		// 	return $this->json(['errors' => "Email and Password are required"]);
		
		// $user = new User();
		// $data = $this->get('jms_serializer')->serialize($user, 'json');

		// $user->setEmail($request->request->get('email'))
		// 	->setPassword(
		// 		$passwordEncoder->encodePassword($user, $plainPassword));
		// $errors = $validator->validate($user);

		// if(count($errors) > 0)
		// 	return $this->json(['errors' => $errors[0]->getMessage()]);

		if (count($violations))
			return ($this->json(['errors' => $violations[0]->getMessage()]));

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user)->flush();
		return $this->json([
			'email' => $request->request->get('email'),
			'user_id' => $user->getId(),
		]);
	}
}
// return new Response('<pre>'.print_r($request->request->all()).'</pre>');
