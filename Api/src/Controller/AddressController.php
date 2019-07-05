<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use App\Entity\Address;

/**
 * @Route("/address", name="address")
 */
class AddressController extends MyAbstractController
{
	/**
	 * @Route("/", name="address")
	 */
	public function index(Request $request)
	{
		$user = $this->findUserOrFail($request);
		if($user->getAddress())
			return $this->json(self::_json($user->getAddress()));
		else
			return $this->json(null);
	}

	/**
	 * @Route("", name="new_address", methods={"POST"})
	 */
	public function createAddress(Request $request)
	{
		if($request->headers->get('token') === null)
			return $this->json(null);
		$user = $this->findUserOrFail($request);
		$street = $request->request->get('street');
		$pc = $request->request->get('pc');

		if($user->getAddress()) {
			$address = $user->getAddress();
			$address->setStreet($street);
			$address->setPc($cp);
		}
		else {
			$address = new Address();
			$address->setUser($user);
			$address->setStreet($street);
			$address->setPc($cp);
		}
		$em = $this->getDoctrine()->getManager();
		$em->persist($address);
		$em->flush();

		return $this->json(self::_json($user->getAddress()));
	}

	private function _json(Address $address)
	{
		return [
			'street' => $address->getStreet(),
			'pc' => $address->getPc()
		];
	}
}
