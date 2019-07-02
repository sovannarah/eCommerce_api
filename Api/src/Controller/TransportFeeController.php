<?php

namespace App\Controller;

use App\Entity\{TransportMode, TransportOffer, SpecOffer};
use App\Repository\TransportModeRepository;
//use Symfony\Component\Debug\Exception\UndefinedFunctionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class TransportFeeController
 * @package App\Controller
 * @Route("/transport")
 */
class TransportFeeController extends MyAbstractController
{
	/**
	 * @Route("", name="getTransporters", methods={"GET"})
	 */
	public function		getTransporters(Request $req,
		TransportModeRepository $rTransport)
	{
		try
		{
			$this->findUserOrFail($req, true);
//			var_dump($rTransport->find(1)->getName());
//			dd($rTransport->findAll());
			return ($this->json($rTransport->getAll()));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("", name="create transport_fee" ,methods={"POST"})
	 */
	public function     create(Request $req)
	{
//		var_dump($req->request->all());
		$tUnity = ['EUR', 'DOL', 'kg', 'g', 'cm', 'm'];
		$entity = [
			[TransportMode::class, 'TransportMode'],
			[TransportOffer::class, 'TransportOffer'],
			[SpecOffer::class, 'SpecOffer']];
		try
		{
			$user = $this->findUserOrFail($req, true);
			return ($this->json($this->recMakeTransport($entity,
				$tUnity,
				$req->request->all())
				, 200));
		} catch (\Exception $e)
		{
			if ($e instanceof  HttpExceptionInterface)
				$status = $e->getStatusCode();
			else
				$status = 400;
			return ($this->json($e->getMessage(), $status));
		}
	}

	/**
	 * @param $tEntity
	 * @param $tUnity
	 * @param $treq
	 * @param int $count
	 * @return TransportMode
	 */
	private function    recMakeTransport($tEntity, $tUnity, $treq, $count = 0)
	{
		$entity = new $tEntity[$count][0]();
		foreach ($treq as $key => $value)
		{
			if (is_array($value) && isset($tEntity[$count]))
			{
				$c = -1;
				while (isset($value[++$c]))
				{
					$recEntity = $this->recMakeTransport($tEntity, $tUnity,
						$value[$c], $count + 1);
					$entity->{'add' .   $tEntity[$count + 1][1]}($recEntity);
					$manager = $this->getDoctrine()->getManager();
					$manager->persist($entity);
				}
				if ($entity instanceof TransportMode)
					$manager->flush();
			}
			else
				$this->TransportEngine($entity, $key, $value);
		}
		return ($entity);
	}

	/**
	 * @param $entity
	 * @param $key
	 * @param $value
	 */
	private function    TransportEngine(&$entity, $key, $value)
	{
		if ($key === 'name')
			$entity->setName($value);
		else if ($entity instanceof SpecOffer)
		{
			if ($key === 'minValue')
				$entity->setMinValue($value);
			else if ($key === 'price')
				$entity->setPrice($value);
			else if ($key === 'unity')
				$entity->setUnity($value);
		}
	}
}
