<?php

namespace App\Controller;

use App\Entity\{Article, StockOrder, StockOrderItem};
use App\Repository\{ArticleRepository, StockOrderRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpKernel\Exception\{
	AccessDeniedHttpException,
	BadRequestHttpException,
	NotFoundHttpException,
	UnauthorizedHttpException};
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StockOrderController
 *
 * @package App\Controller
 *
 * @Route("/stock/order", name="stock_order_")
 */
class StockOrderController extends MyAbstractController
{
	/**
	 * @Route("", name="readall", methods={"GET"})
	 * @param Request $request
	 * @param StockOrderRepository $sORep
	 * @return JsonResponse
	 */
	public function index(Request $request, StockOrderRepository $sORep): JsonResponse
	{
		try {
			$this->findUserOrFail($request, true);
		} catch (AccessDeniedHttpException | UnauthorizedHttpException $e) {
			return $this->json($e->getMessage(), $e->getStatusCode());
		}

		return $this->json($sORep->findBy([], ['send' => 'DESC']));
	}

	/**
	 * @Route("/{id}", name="read", methods={"GET"})
	 * @param Request $request
	 * @param StockOrder $so
	 * @return JsonResponse
	 */
	public function read(Request $request, StockOrder $so): JsonResponse
	{
		try {
			$this->findUserOrFail($request, true);
		} catch (AccessDeniedHttpException | UnauthorizedHttpException $e) {
			return $this->json($e->getMessage(), $e->getStatusCode());
		}

		return $this->json($so);
	}


	/**
	 * @Route("", name="create", methods={"POST"})
	 * @param Request $request
	 * @param EntityManagerInterface $eManager
	 * @return JsonResponse
	 */
	public function create(Request $request, EntityManagerInterface $eManager): JsonResponse
	{
		$so = new StockOrder();
		try {
			$so->setUser($this->findUserOrFail($request, true));
			static::setItems($so, $request, $eManager);
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
	 * @param Request $request
	 * @param EntityManagerInterface $eManager
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException
	 * @uses initItem()
	 */
	private static function setItems(
		StockOrder $so,
		Request $request,
		EntityManagerInterface $eManager
	): void {
		$articleRep = $eManager->getRepository(Article::class);
		foreach ($request->request->all() as $itemData) {
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
