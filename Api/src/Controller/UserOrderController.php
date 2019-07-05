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
use Symfony\Component\HttpClient\HttpClient;

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
	private $address = ['27 rue saint-ambroise 75011'];
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
	 * @Route("/check", name="check_address", methods={"POST"});
	 * @param Request $quest
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	public function     getPriceOnAdresss(Request $quest)
	{
		var_dump($quest->request->all());
		$adress2 = '&ad2=';
		$reqText = 'http://127.0.0.1:5000/distance?ad1=' + $this->address + $adress2;
		$httpClient = HttpClient::create();
		$request = $httpClient->request('GET', $reqText);

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
	public function create(Request $request, EntityManagerInterface $eManager, \Swift_Mailer $mailer): JsonResponse
	{
		$uo = new UserOrder();
		try {
			$uo->setUser($this->findUserOrFail($request));
			static::setItems($uo, $request, $eManager, $mailer);
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
		Request $request,
		EntityManagerInterface $eManager,
		\Swift_Mailer $mailer
	): void {
		$articleRep = $eManager->getRepository(Article::class);
		foreach ($request->request->all() as $itemData) {
			$sOItem = static::initItem($itemData, $articleRep, $eManager, $mailer);
			// dd($sOItem);
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
	private static function initItem(
		$itemData,
		ArticleRepository $articleRep,
		EntityManagerInterface $eManager,
		\Swift_Mailer $mailer
	): UserOrderItem {
		if (!isset($itemData['id'], $itemData['quantity']))
			throw new BadRequestHttpException('missing id and/or quantity on an item');
		$article = $articleRep->find($itemData['id']);
		if (!$article)
			throw new NotFoundHttpException('Could not find Article with id: '.$itemData['id']);
		$article->setStock($article->getStock()-$itemData['quantity']);

		$eManager->persist($article);
		$eManager->flush();

		static::checkStock($article, $mailer);

		return (new UserOrderItem())
			->setArticle($article)
			->setQuantity($itemData['quantity']);
	}

	private static function	checkStock(Article $article, \Swift_Mailer $mailer) :void
	{
		$stock = $article->getStock();
		echo "Article ".$article->getTitle()." => encore ".$stock." en stock";
		if ($stock <= 10)
			static::sentRestockEmailAlert($article->getTitle(), $stock, $mailer);
	}

	private static function	sentRestockEmailAlert(
		$title,
		$stock,
		\Swift_Mailer $mailer
	): void {
		echo " Refiling";
		$message = (new \Swift_Message("Restock $title"))
			->setFrom('cyrilcorpcomputers@gmail.com')
			->setTo('cyrilcorpcomputers@gmail.com')
			->setBody(
				"<html>
					<p>The $title stock is very low ($stock), you may want to restock it on your admin page.</p>
				</html>",
				'text/html'
			);
		$mailer->send($message);
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
