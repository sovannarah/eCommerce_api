<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
// use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \JMS\Serializer\SerializationContext;
use \JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;


class AuthController extends AbstractFOSRestController
{
	private $_salt;

	public function __construct() {
		$this->_salt = "bien muniTIon_disTrait organisateur OrDoNaNces";
	}

	/**
	 * @Rest\Post("/login", name="api_login")
	 * 
	 * @param entity $request             Request instance
	 * @param entity $authenticationUtils AuthenticationUtils instance
	 * @param entity $passwordEncoder     The Password encoder
	 * 
	 * @return json User email, user role and generated token
	 */
	public function login(
		Request $request,
		AuthenticationUtils $authenticationUtils,
		UserPasswordEncoderInterface $passwordEncoder
	) {
		extract($this->authenticator($request, $passwordEncoder)); //creates $errors, $user, $token, $expire
		// $res); 
		/*
			$errors = $res['errors'] ?? null;
			$user = $res['user'] ?? null;
			$token = $res['token'] ?? null;
		*/

		if (isset($errors))
			return $this->json(['errors' => $errors]);

		$user->setToken($token);
		$user->setTokenExpiration(new \DateTime(date("Y-m-d H:i:s", $expire)));
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user);
		$entityManager->flush();

		return $this->json([
			'email' => $user->getEmail(),
			'role' => $user->getRoles(),
			'token' => $token
		]);
	}

	/**
	 * Checks if users informations are correct: does any user exists and is password valid
	 * Gets a token (decode it using unserialize(base64_decode($token)) )
	 * 
	 * @param entity $request         Request instance
	 * @param entity $passwordEncoder The Password encoder
	 * 
	 * @return array User entity, generated token and token expiration date
	 */
	public function authenticator($request, $passwordEncoder) {
		$credentials = [
			'email' => $request->request->get('email'),
			'password' => $request->request->get('password'),
		];

		$user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
		if (!$user)
			return (['errors' => 'Email could not be found.']);

		$validPassword = $this->checkPassword($credentials, $user, $passwordEncoder);
		if (!$validPassword)
			return (['errors' => 'Invalid password.']);

		extract($this->tokenGenerator(2, $user->getEmail()));
		/*
			$request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);
			$request->getSession()->set('user_token', $token);
			var_dump($request->getSession()->get(Security::LAST_USERNAME));
			var_dump($request->getSession()->get('user_token'));
		*/
		return (['user' => $user, 'token' => $token, 'expire' => $expire]);
	}

	/**
	 * Check if password stored in DB matches with auth password
	 * 
	 * @param array  $credentials     Users submited informations from request
	 * @param entity $user            User instance
	 * @param entity $passwordEncoder The Password encoder
	 * 
	 * @return boolean Is password valid
	 */
	public function checkPassword($credentials, $user, $passwordEncoder) {
		return ($passwordEncoder->isPasswordValid($user, $credentials['password']));
	}

	/**
	 * Generates a unique token for the user using random bytes, timestamp and email
	 * 
	 * @param int    $expDays In how many days token is supposed to be invalid
	 * @param string $email   The user email
	 * 
	 * @return array The encoded token and the expiration date
	 */
	public function tokenGenerator($expDays, $email) {
		$expires = time() + (($expDays*24)*60*60); //2 days = 48h * 60m * 60s
		// var_dump(date('d-m-Y  G:i:s', $expires));
		$token['rand'] = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
		$token['expires'] = $expires;
		$token['email'] = $email;
		return(['token' => base64_encode(serialize($token)), 'expire' => $expires]);
	}

	/**
	 * Creates a new user entry after data validation (FOS) and password encoding
	 * 
	 * @Rest\Post("/register", name="api_register")
	 * @Rest\View
	 * @ParamConverter("user", converter="fos_rest.request_body")
	 * 
	 * @param entity $user            
	 * @param entity $violations      An array of user's entity constraints violations
	 * @param entity $passwordEncoder The Password encoder
	 * 
	 * @return json The first violation message if there is any, or email and id of new user
	 */
	public function register(
		User $user,
		ConstraintViolationList $violations,
		UserPasswordEncoderInterface $passwordEncoder
	): Response {
		/*
			$userEmail = $request->request->get('email');
			$plainPassword = $request->request->get('password');
			if(!$userEmail || !$plainPassword)
				return $this->json(['errors' => "Email and Password are required"]);
			
			$user = new User();

			$user->setEmail($request->request->get('email'))
				->setPassword(
					$passwordEncoder->encodePassword($user, $plainPassword));
			$errors = $validator->validate($user);

			if(count($errors) > 0)
				return $this->json(['errors' => $errors[0]->getMessage()]);
		*/
		if (count($violations))
			return ($this->json(['errors' => $violations[0]->getMessage()]));

		$user->setPassword(
			$passwordEncoder->encodePassword($user, $user->getPassword())
		);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user);
		$entityManager->flush();
		return $this->json([
			'email' => $user->getEmail('email'),
			'user_id' => $user->getId(),
		]);
	}
}
// return new Response('<pre>'.print_r($request->request->all()).'</pre>');
