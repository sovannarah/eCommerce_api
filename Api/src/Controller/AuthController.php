<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse,
	Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface,
	Symfony\Component\Security\Http\Authentication\AuthenticationUtils,
	App\Entity\User,
	FOS\RestBundle\Controller\Annotations as Rest,
	FOS\RestBundle\Controller\AbstractFOSRestController,
	Symfony\Component\HttpFoundation\Response,
	Symfony\Component\HttpFoundation\Request,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
	Symfony\Component\Validator\ConstraintViolationList;
	// use FOS\RestBundle\Controller\FOSRestController;


class AuthController extends AbstractFOSRestController
{
	private $_salt;

	public function __construct() {
		$this->_salt = "bien muniTIon_disTrait organisateur OrDoNaNces";
	}

	/**
	 * @Rest\Post("/login", name="api_login")
	 *
	 * @param Request $request Request instance
	 * @param AuthenticationUtils $authenticationUtils AuthenticationUtils instance
	 * @param UserPasswordEncoderInterface $passwordEncoder The Password encoder
	 *
	 * @return JsonResponse User email, user role and generated token
	 */
	public function login(
		Request $request,
		AuthenticationUtils $authenticationUtils,
		UserPasswordEncoderInterface $passwordEncoder
	) {
		extract($this->authenticator($request, $passwordEncoder)); //creates $errors, $user, $token, $expire

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
	 * Checks if users information are correct: does any user exists and is password valid
	 * Gets a token (decode it using unserialize(base64_decode($token)) )
	 *
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 *
	 * @return array User entity, generated token and token expiration date
	 */
	public function authenticator(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
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

		extract(self::tokenGenerator(2, $user->getEmail()));

		return (['user' => $user, 'token' => $token, 'expire' => $expire]);
	}

	/**
	 * Check if password stored in DB matches with auth password
	 *
	 * @param array $credentials Users submited informations from request
	 * @param $user            User instance
	 * @param UserPasswordEncoderInterface $passwordEncoder The Password encoder
	 *
	 * @return boolean Is password valid
	 */
	public function checkPassword(array $credentials, User $user, UserPasswordEncoderInterface $passwordEncoder) {
		return ($passwordEncoder->isPasswordValid($user, $credentials['password']));
	}

	/**
	 * Generates a unique token for the user using random bytes, timestamp and email
	 *
	 * @param int $expDays In how many days token is supposed to be invalid
	 * @param string $email The user email
	 *
	 * @return array The encoded token and the expiration date
	 */
	public static function tokenGenerator($expDays, $email) {
		$expires = time() + (($expDays*24)*60*60); //2 days = 48h * 60m * 60s
		// var_dump(date('d-m-Y  G:i:s', $expires));
		try {
			$rand = random_bytes(32);
		} catch (\Exception $e) {
			throw new \Exception('Alex was wrong in ' . __CLASS__ . __METHOD__, 0, $e);
		}
		$token['rand'] = rtrim(strtr(base64_encode($rand), '+/', '-_'), '=');
		$token['expires'] = $expires;
		$token['email'] = $email;
		return (['token' => base64_encode(serialize($token)), 'expire' => $expires]);
	}

	/**
	 * Creates a new user entry after data validation (FOS) and password encoding
	 *
	 * @Rest\Post("/register", name="api_register")
	 * @Rest\View
	 * @ParamConverter("user", converter="fos_rest.request_body")
	 *
	 * @param User $user
	 * @param ConstraintViolationList $violations An array of user's entity constraints violations
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 *
	 * @return JsonResponse The first violation message if there is any, or email and id of new user
	 */
	public function register(
		User $user,
		ConstraintViolationList $violations,
		UserPasswordEncoderInterface $passwordEncoder
	): Response {
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
