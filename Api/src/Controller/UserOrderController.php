<?php

namespace App\Controller;

use App\Entity\{AbstractOrder,
	Article,
	TransportMode,
	UserOrder,
	UserOrderItem,
	VariantArticle};
use App\Repository\ArticleRepository;
use App\Repository\TransportModeRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\TransportModeRepository;
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
	private $address = ['27 rue Saint-Ambroise 75011'];
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
	//	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 * @param TransportModeRepository $rTransport
	 * @param EntityManagerInterface $manager
	 * @return JsonResponse
	 * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	public function     getPriceOnAdresss(Request $quest,
		TransportModeRepository $rTransport, EntityManagerInterface $manager)
	{
		try
		{
			$req = $quest->request->all();
			$adress2 = '&ad2=' . $req['address'] . ' ' . $req['pc'];
			$transport = $rTransport->findOneBy(['id' => 8]);
			$reqText = 'http://127.0.0.1:5000/distance?ad1=' . $this->address[0] . $adress2;
			$httpClient = HttpClient::create();
			$request = $httpClient->request('GET', $reqText);
			$distance = json_decode($request->getContent(), true);
			$tPrice = $this->getPriceOfOffers($transport, $distance['distance'],
				$req['articles'], $manager);
			return($this->json($tPrice));
		} catch (\Exception $e)
		{
			return ($this->json($e->getMessage()));
		}
	}

	private function     getPriceOfOffers($transport, $distance,
		array $articles, EntityManagerInterface $manager)
	{
		$articleR = $manager->getRepository(Article::class);
		$variantR = $manager->getRepository(VariantArticle::class);
		$offerTable = $transport->getTransportOffers();
		$tResponse = [];
		$total = 0;
		$c3 = -1;
		$len3 = count($offerTable);
		while (++$c3 < $len3)
		{
			$nOrder = new UserOrder();
			$c1 = -1;
			$weightArticle = 0;
			$len1 = count($articles);
			while (++$c1 < $len1)
			{
				$nOrderItems = new UserOrderItem();
				$article = $articleR->find($articles[$c1]['id']);
				$quantity = (int) $articles[$c1]['quantity'];
				if ($article->getKg() > 0)
					$weightArticle = $weightArticle + $article->getKg() / 100;
				$price = $article->getPrice();
				if (isset($articles[$c1]['variants']))
				{
					$c2 = -1;
					$len2 = count($articles[$c1]['variants']);
					while (++$c2 < $len2)
					{
						$variant = $variantR->find($articles[$c1]['variants'][$c2]['id']);
						if ($article->getId() === $variant->getParent()->getId())
							$price = $price + ($variant->getVarPrice() / 100);
					}
				}
				if ($quantity > 0)
				{
					$total = $total + ($price * $quantity);
					$nOrderItems->setArticle($article);
					$nOrderItems->setQuantity($quantity);
					$nOrder->addOrderItem($nOrderItems);
					$manager->persist($nOrderItems);
				}
			}
			$specsOffer = $offerTable[$c3]->getSpecOffers();
			$c4 = -1;
			$len4 = count($specsOffer);
			while (++$c4 < $len4)
			{
				if ($specsOffer[$c4]->getName() === 'Weight')
				{
					if ($weightArticle > $specsOffer[$c4]->getMinValue())
						$total = $total + ($weightArticle * ($specsOffer[$c4]->getPrice() / 100));
					else
						$total = $total + ($specsOffer[$c4]->getMinPrice() / 100);
				}
				else if ($specsOffer[$c4]->getName() === 'Distance')
				{
					if ($distance > $specsOffer[$c4]->getMinValue())
						$total = $total + ($distance * ($specsOffer[$c4]->getPrice() / 100));
					else
						$total = $total + ($specsOffer[$c4]->getMinPrice() / 100);
				}
			}
			$nOrder->setTotal($total * 100);
			$manager->persist($nOrder);
			$manager->flush();
			$manager->refresh($nOrder);
			$tResponse[] = [
				'offerId' => $offerTable[$c3]->getId(),
				'price' => $total * 100,
				'id' => $nOrder->getId()
			];
		}
		return ($tResponse);
	}
//	private function
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
	 * @param $itemData
	 * @param ArticleRepository $articleRep
	 * @param EntityManagerInterface $eManager
	 * @param \Swift_Mailer $mailer
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

	//region Payment
	/**
	 * @Route("/{id}/pay", methods={"POST"})
	 * @param Request $request
	 * @param UserOrder $uo
	 * @return JsonResponse
	 * @uses getEmail
	 */
	private function pay(Request $request, UserOrder $uo): JsonResponse
	{
		if ($uo->getSend()) {
			throw new BadRequestHttpException('Order already payed');
		}
		Stripe::setApiKey('sk_test_Rp1hCFXgQw3x7ZnR8NvBP0aq000x2BmKPK');
		$uo->setTotal($uo->getTotal() + static::getTransportPrice($request));
		try {
			return $this->json(Charge::create(
				[
					'amount' => $uo->getTotal(),
					'currency' => 'eur',
					'receipt_email' => static::getEmail($request, $uo),
					'source' => $request->request->get('cardToken'),
				]
			));
		} catch (BadRequestHttpException $e) {
			return $this->errJson($e);
		} catch (StripeException $e) {
			return $this->json($e->getJsonBody(), $e->getHttpStatus());
		}
	}


	/**
	 * @param Request $request
	 * @param AbstractOrder $order
	 * @return string
	 * @throws BadRequestHttpException
	 * @used-by pay
	 */
	private static function getEmail(Request $request, AbstractOrder $order): string
	{
		$email = $request->request->get('email');
		if (!$email) {
			$user = $order->getUser();
			if (!$user) {
				throw new BadRequestHttpException('Must supply email for anonymous user');
			}
			$email = $user->getEmail();
		}
		return $email;
	}

	/**
	 * @param Request $request
	 * @return int
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException
	 * @used-by pay
	 */
	private static function getTransportPrice(Request $request): int
	{
		//TODO get price using id (with validation)
		return 0;
	}
	//endregion Payment

}
