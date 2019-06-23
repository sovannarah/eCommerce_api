<?php

namespace App\Controller;

use App\Entity\StockOrder;
use App\Repository\ArticleRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;

/**
 * Class OrderController
 * @package App\Controller
 * @Rest\Route("/order")
 */
class OrderController extends AbstractController
{
	/**
	 * @Rest\Route ("", name="addOrder", methods={"POST"})
	 */
	public function addOrder(Request $request, ArticleRepository $rArticle)
	{
		try
		{
			$this->_findAdminOrFail($request);
			$date = new \DateTime('now');
			$nOrder = new StockOrder();
			if (($article = $this->engineRequest($rArticle, $request->request->get('articles'))) === false)
				return ($this->json(["error" => "bad request"], 404));
		}catch (\Exception $e)
		{
			if ($e instanceof HttpExceptionInterface)
				$statusCode = $e->getStatusCode();
			else
				$statusCode = 400;
			return $this->json($e->getMessage(), $statusCode);
		}
	}
	private function    engineRequest($rArticle, $table)
	{
		$c = -1;
		$ltable = count($table);
		$tArticles = [];
		while (++$c < $ltable)
		{
			$article = $rArticle->find($table[$c]['id']);
			if (!$article)
				return (false);
			$tArticles[] = $article;
		}
		var_dump($tArticles);
		if (empty($tArticles))
			return (false);
		return ($tArticles);
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
