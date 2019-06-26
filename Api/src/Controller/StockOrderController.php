<?php

namespace App\Controller;

use App\Entity\{Article, StockOrder, StockOrderItem};
use App\Repository\ArticleRepository;
use App\Repository\StockOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpKernel\Exception\{
	AccessDeniedHttpException,
	BadRequestHttpException,
	NotFoundHttpException,
	UnauthorizedHttpException};
use Symfony\Component\Routing\Annotation\Route;

class StockOrderController extends MyAbstractController
{
	/**
	 * @Route("/admin/order", name="stock_order")
	 * @param Request $request
	 * @param StockOrderRepository $sORep
	 * @return JsonResponse
	 */
	public function index(Request $request, StockOrderRepository $sORep): JsonResponse
	{
		try {
			$this->_findAdminOrFail($request);
		} catch (AccessDeniedHttpException | UnauthorizedHttpException $e) {
			return $this->json($e->getMessage(), $e->getStatusCode());
		}

		return $this->json($sORep->findBy([], ['send' => 'DESC']));
	}

	public function create(Request $request, EntityManagerInterface $eManager): JsonResponse
	{
		$so = new StockOrder();
		try {
			$so->setUser($this->_findAdminOrFail($request));
			static::setItems($so, $request->request->get('items'), $eManager);
		} catch (AccessDeniedHttpException | UnauthorizedHttpException $e) {
			return $this->json($e->getMessage(), $e->getStatusCode());
		}
		$eManager->persist($so);
		$eManager->flush();
		$eManager->refresh($so);
		return $this->json($so, 201);
	}

	/**
	 * @param StockOrder $so
	 * @param string[][] $itemDatas
	 * @param EntityManagerInterface $eManager
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException
	 * @uses initItem()
	 */
	private static function setItems(
		StockOrder $so,
		$itemDatas,
		EntityManagerInterface $eManager
	): void {
		if (!is_iterable($itemDatas)) {
			throw new BadRequestHttpException('Invalid param type for items');
		}
		$articleRep = $eManager->getRepository(Article::class);
		foreach ($itemDatas as $itemData) {
			$sOItem = static::initItem($itemData, $articleRep);
			$so->addOrderItem($sOItem);
			$eManager->persist($sOItem);
		}
	}

	/**
	 * @param string[] $itemData containing fields 'id' and 'quantity'
	 * @param ArticleRepository $articleRep
	 * @return StockOrderItem
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException if no article found with given id
	 */
	private static function initItem($itemData, ArticleRepository $articleRep): StockOrderItem
	{
		if (!isset($itemData['id'], $itemData['quantity'])) {
			throw new BadRequestHttpException('missing id and/or quantity on an item');
		}
		$item = $articleRep->find($itemData['id']);
		if (!$item) {
			throw new NotFoundHttpException('Could not find Article with id: '.$itemData['id']);
		}

		return (new StockOrderItem())
			->setArticle($item)
			->setQuantity($itemData['quantity']);
	}
}
