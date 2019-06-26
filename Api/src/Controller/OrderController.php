<?php

namespace App\Controller;

use App\Entity\{StockOrder, StockOrderItem};
use App\Repository\{ArticleRepository, StockOrderRepository};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request,JsonResponse};

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/order")
 */
class OrderController extends AbsCheckUserController
{

	/**
	 * @Route("", methods={"GET"})
	 * @param StockOrderRepository $order
	 * @return JsonResponse
	 */
	public function     getOrders(StockOrderRepository $order): JsonResponse
	{
		return($this->json($order->findBy([], ['send' => 'DESC'])));
	}

	/**
	 * @Route("/{id}", name="get_order", methods={"GET"})
	 */
	public function     getOrder(StockOrder $order): JsonResponse
	{
		return ($this->json($order));
	}

	/**
	 * @Route("", name="addOrder", methods={"POST"})
	 * @param Request $request
	 * @param ArticleRepository $rArticle
	 * @return JsonResponse
	 * @throws \Exception
	 */
	public function     addOrder(Request $request, ArticleRepository $rArticle)
	{
			$user = $this->isAdmin($request);
			if ($user instanceof JsonResponse)
				return ($user);
			$manager = $this->getDoctrine()->getManager();
			$ordersItem = $this->makeOrder($rArticle,
				$request->request->get('articles'));
			if ($ordersItem === false)
				return ($this->json('bad Request', 404));
			else
			{
				$manager->persist($ordersItem);
				$manager->flush();
				return($this->json($ordersItem));
			}
	}

	/**
	 * @param $rArticle
	 * @param $tArticles
	 * @return StockOrder|bool
	 */
	private function    makeOrder(ArticleRepository $rArticle, array $tArticles)
	{
		$c = -1;
		$ltable = count($tArticles);
		$order = new StockOrder();
		$order->setStatus(false);
		while (++$c < $ltable)
		{
			if (!isset($tArticles[$c]['id'], $tArticles[$c]['number']))
				return (false);
			$article = $rArticle->find($tArticles[$c]['id']);
			if (!$article)
				return (false);
			$item = new StockOrderItem();
			$item->setArticle($article);
			$item->setQuantity((int) $tArticles[$c]['number']);
			$order->addOrderItem($item);
		}
		return ($order);
	}
}
