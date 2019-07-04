<?php

namespace App\Controller;

use App\Entity\PromotionCode;
use App\Repository\PromotionCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PromotionCodeController
 * @package App\Controller
 * @Route("/promotionCode", name="promotionCode")
 */
class PromotionCodeController extends MyAbstractController
{
	/**
	 * @Route("", name="getPromotion_code", methods={"GET"})
	 * @param Request $req
	 * @param PromotionCodeRepository $rCode
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function getCodesPromotion(Request $req,PromotionCodeRepository $rCode)
	{
		try
		{
			$this->findUserOrFail($req, true);
			$tcode = $rCode->findAll();
			$tableresponse = [];
			$c = -1;
			$lent = count($tcode);
			while (++$c < $lent)
			{
				$tableresponse[] = [
					'id' => $tcode[$c]->getId(),
					'code' => $tcode[$c]->getCode(),
					'reduction' => $tcode[$c]->getReduction()];
			}
			return ($this->json($tableresponse));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("", name="addPromotion_code", methods={"POST"})
	 * @param Request $req
	 * @param EntityManagerInterface $manager
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     addCodePromotion(Request $req, EntityManagerInterface $manager)
	{
		try
		{
			$reqCode = $req->request->all();
			$this->findUserOrFail($req, true);
			$c = -1;
			$lent = count($reqCode);
			while (++$c < $lent)
			{
				if ((int) $reqCode[$c]['reduction'] < 1 || (int) $reqCode[$c]['reduction'] > 100)
					return ($this->json(['error' => "bad reduction"], 400));
				$code = new PromotionCode();
				$code->setReduction($reqCode[$c]['reduction']);
				$code->setCode($reqCode[$c]['code']);
				$manager->persist($code);
				$manager->flush();
			}
			return($this->json('created', 201));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("/{id}", name="rmPromotion_code", methods={"DELETE"})
	 * @param Request $req
	 * @param PromotionCode $promotionCode
	 * @return \Symfony\Component\HttpFoundation\JsonResponse]
	 */
	public function     rmCodePromotion(Request $req, PromotionCode $promotionCode)
	{
		try
		{
			$this->findUserOrFail($req, true);
			$manager = $this->getDoctrine()->getManager();
			$manager->remove($promotionCode);
			$manager->flush();
			return ($this->json('deleted'));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}
}
