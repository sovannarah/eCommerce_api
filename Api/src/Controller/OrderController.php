<?php

namespace App\Controller;

use App\Entity\{StockOrder, OrderItems, User};
use App\Repository\ArticleRepository;
use App\Repository\StockOrderRepository;
use function PHPSTORM_META\type;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request,JsonResponse};
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     getOrders(StockOrderRepository $order)
	{
		return($this->json($order->findBy([], ['send' => 'DESC']),200));
	}

	/**
	 * @Route("/{id}", name="get_order", methods={"GET"})
	 */
	public function     getOrder(StockOrder $order)
	{
		return ($this->json($order));
	}

	/**
	 * @Route("", name="addOrder", methods={"POST"})
	 * @param Request $request
	 * @param ArticleRepository $rArticle
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     addOrder(Request $request, ArticleRepository $rArticle)
	{
			$user = $this->isAdmin($request);
			if ($user instanceof JsonResponse)
				return ($user);
			$manager = $this->getDoctrine()->getManager();
			$ordersItem = $this->engineRequest($rArticle,
				$request->request->get('articles'));
			if ($ordersItem === false)
				return ($this->json(['error' => 'bad Request'], 403));
			else
			{
				$manager->persist($ordersItem);
				$manager->flush();
				return($this->json($ordersItem, 200));
			}
	}

	/**
	 * @param $rArticle
	 * @param $table
	 * @return StockOrder|bool
	 * @throws \Exception
	 */
	private function    engineRequest($rArticle, $table)
	{
		$c = -1;
		$ltable = count($table);
		$date = new \DateTime('Europe/Paris');
		$order = new StockOrder();
		$order->setStatus(false);
		$order->setSend($date);
		while (++$c < $ltable)
		{
			if (!isset($table[$c]['id']) || !isset($table[$c]['number']))
				return (false);
			$article = $rArticle->find($table[$c]['id']);
			if (!$article)
				return (false);
			$item = new OrderItems();
			$item->setArticle($article);
			$item->setQuantity((int) $table[$c]['number']);
			$order->getOrderItems()->add($item);
		}
		return ($order);
	}
}
