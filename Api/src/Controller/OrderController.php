<?php

namespace App\Controller;

use App\Entity\StockOrder;
use App\Entity\OrderItems;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/order")
 */
class OrderController extends AbstractController
{

	public function     getOrders()
	{

	}
	/**
	 * @Route ("", name="addOrder", methods={"POST"})
	 */
	public function addOrder(Request $request, ArticleRepository $rArticle)
	{
		try
		{
			$this->_findAdminOrFail($request);
			$manager = $this->getDoctrine()->getManager();
			$ordersItem = $this->engineRequest($rArticle,
				$request->request->get('articles'));
			if ($ordersItem === false)
				return ($this->json(['error' => 'bad Request'], 403));
			else
			{
				$manager->persist($ordersItem);
				$manager->flush();
//				$manager->refresh($ordersItem);
				return($this->json($ordersItem, 200));
			}
		}catch (\Exception $e)
		{
			if ($e instanceof HttpExceptionInterface)
				$statusCode = $e->getStatusCode();
			else
				$statusCode = 400;
			return $this->json($e->getMessage(), $statusCode);
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
		$date = new \DateTime('now');
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

	/**
	 * @param Request $request
	 * @return User
	 * @throws AccessDeniedException | UnauthorizedHttpException
	 */
	private function _findAdminOrFail(Request $request)
	{
		$token = $request->headers->get('token');
		if (!$token) {
			throw new UnauthorizedHttpException('', 'Missing Token');
		}
		$user = $this->getDoctrine()
			->getManager()
			->getRepository(User::class)
			->findAdminByToken($token);
		if (!$user) {
			throw new AccessDeniedHttpException();
		}
		return $user;
	}
}
