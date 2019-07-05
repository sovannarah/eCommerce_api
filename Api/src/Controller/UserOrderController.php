<?php

namespace App\Controller;

use App\Entity\{AbstractOrder, Article, UserOrder, UserOrderItem};
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\{Charge, Error\Base as StripeException, Stripe};
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpKernel\Exception\{
	AccessDeniedHttpException,
	BadRequestHttpException,
	HttpException,
	NotFoundHttpException,
	UnauthorizedHttpException};
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order", name="order_")
 */
class UserOrderController extends MyAbstractController
{
	/**
	 * @Route("", name="allof_user", methods={"GET"})
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse
	{
		try {
			return $this->json(
				$this->findUserOrFail($request)->getUserOrders()->toArray()
			);
		} catch (UnauthorizedHttpException | AccessDeniedHttpException $e) {
			return $this->json($e->getMessage(), $e->getStatusCode());
		}
	}

	/**
	 * @Route("/{id}", name="read", methods={"GET"})
	 * @param Request $request
	 * @param UserOrder $order
	 * @return JsonResponse
	 */
	public function read(Request $request, UserOrder $order): JsonResponse
	{
		try {
			$user = $this->findUserOrFail($request);
			if ($user->getId() !== $order->getUser()->getId()) {
				throw new AccessDeniedHttpException('Not your order');
			}

			return $this->json($order);
		} catch (UnauthorizedHttpException|AccessDeniedHttpException $e) {
			return $this->errJson($e);
		}
	}

	/**
	 * @Route("", name="create", methods={"POST"})
	 * @param Request $request
	 * @param EntityManagerInterface $eManager
	 * @return JsonResponse
	 */
	public function create(Request $request, EntityManagerInterface $eManager): JsonResponse
	{
		$postBag = $request->request;
		$uo = new UserOrder();
		try {
			$uo->setUser($this->tryFindUser($request));
			static::setItems($uo, $postBag->get('items'), $eManager);
		} catch (HttpException $e) {
			return $this->errJson($e);
		}
		$eManager->persist($uo);
		$eManager->flush();
		$eManager->refresh($uo);

		return $this->json($uo, 201);
	}


	/**
	 * @param AbstractOrder $order
	 * @param string[][] $itemsDatas
	 * @param EntityManagerInterface $eManager
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException
	 * @uses initItem()
	 */
	private static function setItems(
		AbstractOrder $order,
		array $itemsDatas,
		EntityManagerInterface $eManager
	): void {
		if (!\count($itemsDatas)) {
			throw new BadRequestHttpException('No items');
		}
		$articleRep = $eManager->getRepository(Article::class);
		foreach ($itemsDatas as $itemData) {
			$sOItem = static::initItem($itemData, $articleRep);
			$order->addOrderItem($sOItem);
			$eManager->persist($sOItem);
		}
	}

	/**
	 * @param string[] $itemData containing fields 'id' and 'quantity'
	 * @param ArticleRepository $articleRep
	 * @return UserOrderItem
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException if no article found with given id
	 */
	private static function initItem($itemData, ArticleRepository $articleRep): UserOrderItem
	{
		$qtt = static::filterNaturalInt($itemData['quantity']);
		if (!isset($itemData['id'], $qtt)) {
			throw new BadRequestHttpException('missing/invalid id and/or quantity on an item');
		}
		$item = $articleRep->find($itemData['id']);
		if (!$item) {
			throw new NotFoundHttpException('Could not find Article with id: '.$itemData['id']);
		}

		return (new UserOrderItem())
			->setArticle($item)
			->setQuantity($qtt);
	}

	/**
	 * @Route("/{id}/pay", methods={"POST"})
	 * @param Request $request
	 * @param UserOrder $uo
	 * @return JsonResponse
	 */
	private function pay(Request $request, UserOrder $uo): JsonResponse
	{
		if ($uo->getSend()) {
			throw new BadRequestHttpException('Order already payed');
		}
		Stripe::setApiKey('sk_test_Rp1hCFXgQw3x7ZnR8NvBP0aq000x2BmKPK');
		$email = $request->request->get('email');
		if (!$email) {
			$user = $uo->getUser();
			if (!$user) {
				return $this->json('Must supply email for anonymous user', 400);
			}
			$email = $user->getEmail();
		}
		$uo->setTotal($uo->getTotal() + static::getTransportPrice($request));
		try {
			return $this->json(Charge::create(
				[
					'amount' => $uo->getTotal(),
					'currency' => 'eur',
					'receipt_email' => $email,
					'source' => $request->request->get('cardToken'),
				]
			));
		} catch (StripeException $e) {
			return $this->json($e->getJsonBody(), $e->getHttpStatus());
		}
	}

	/**
	 * @param Request $request
	 * @return int
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException
	 */
	private static function getTransportPrice(Request $request): int
	{
		//TODO get price using id (with validation)
		return 0;
	}

}
