<?php

namespace App\Controller;

use App\Entity\SpecsOffer;
use App\Entity\SpecsOfferPrice;
use App\Entity\TransportFee;
use App\Entity\TransportOffer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TransportFeeController
 * @package App\Controller
 * @Route("/transport")
 */
class TransportFeeController extends MyAbstractController
{
	/**
//	 * @Route("/transport/fee", name="transport_fee")
	 */
	public function index()
	{
		return $this->json([
			'message' => 'Welcome to your new controller!',
			'path' => 'src/Controller/TransportFeeController.php',
		]);
	}
	/**
	 * @Route("", name="create transport_fee" ,methods={"POST"})
	 */
	public function     create(Request $req)
	{
		var_dump($req->request->all());
		$tUnity = ['EUR', 'DOL', 'kg', 'g', 'cm', 'm'];
		$entity = ['TransportFee','TransportOffer', 'SpecsOffer',
			'SpecsOfferPrice'];
//		return ($this->recMakeTransport($entity, $tUnity, $req->request->all()));
	}

	private function    recMakeTransport($tEnity, $tUnity, $treq, $count = 0)
	{
		$entity = new $tEnity[$count]();
		foreach ($treq as $key => $value)
		{
			if ($key !== 'value' && is_array($value))
			{
				$manager = $this->getDoctrine()->getManager();
				try
				{
					$recEntity = $this->RecMakeTransport($tEnity,$tUnity, $value, $count + 1);
					$entity->{"add" . $tEnity[$count + 1]}($recEntity);
					$manager->persist($entity);
					$manager->flush();
				} catch (\Exception $e)
				{
					if ($e instanceof HttpExceptionInterface)
						$statusCode = $e->getStatusCode();
					else
						$statusCode = 400;
					return ($this->json($e->getMessage(), $statusCode));
				}
			}
			else if ($key === 'name')
				$entity->setName($value);
			else if ($key === 'unity' && in_array($value, $tUnity))
				$entity->setUnity($value);
			if ($key === 'value')
			{
				foreach ($value as $pkey => $pval)
				{
				}
				return ($tEnity[0]);
			}
		}
		return ($entity);
	}
}
