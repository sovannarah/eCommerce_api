<?php

namespace App\Controller;

use App\Entity\{AbstractOrder, Article, UserOrder, UserOrderItem};
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpKernel\Exception\{AccessDeniedHttpException,
	BadRequestHttpException,
	HttpException,
	NotFoundHttpException,
	UnauthorizedHttpException};
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserOrderController
 *
 * @package App\Controller
 *
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
		$uo = new UserOrder();
		try {
			$uo->setUser($this->findUserOrFail($request));
			static::setItems($uo, $request, $eManager);
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
	 * @param Request $request
	 * @param EntityManagerInterface $eManager
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException
	 * @uses initItem()
	 */
	private static function setItems(
		AbstractOrder $order,
		Request $request,
		EntityManagerInterface $eManager
	): void {
		$articleRep = $eManager->getRepository(Article::class);
		foreach ($request->request->all() as $itemData) {
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
		if (!isset($itemData['id'], $itemData['quantity'])) {
			throw new BadRequestHttpException('missing id and/or quantity on an item');
		}
		$item = $articleRep->find($itemData['id']);
		if (!$item) {
			throw new NotFoundHttpException('Could not find Article with id: '.$itemData['id']);
		}

		return (new UserOrderItem())
			->setArticle($item)
			->setQuantity($itemData['quantity']);
	}
}
