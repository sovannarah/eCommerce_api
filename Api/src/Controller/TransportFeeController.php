<?php

namespace App\Controller;

use App\Entity\{TransportMode, TransportOffer, SpecOffer};
use App\Repository\TransportModeRepository;
//use Symfony\Component\Debug\Exception\UndefinedFunctionException;
use Doctrine\ORM\EntityManagerInterface;
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
			return ($this->json($rTransport->getAll()));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("/{id}", name="delTransporters", methods={"DELETE"})
	 * @param Request $req
	 * @param TransportMode $transport
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     delteTransport(Request $req, TransportMode $transport,
		EntityManagerInterface $manager)
	{
		try
		{
			$this->findUserOrFail($req, true);
			$manager->remove($transport);
			$manager->flush();
			return ($this->json("deleted", 200));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("/{id}/spec", name="delete_spec", methods={"DELETE"})
	 * @param Request $req
	 * @param SpecOffer $spec
	 * @param EntityManagerInterface $manager
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     deleteSpec(Request $req, SpecOffer $spec,
		EntityManagerInterface $manager)
	{
		try
		{
			$this->findUserOrFail($req, true);
			$offer = $spec->getOffer();
			$offer->removeSpecOffer($spec);
			$manager->persist($offer);
			$manager->flush();
			return ($this->json("deleted", 200));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	/**
	 * @Route("/{id}/offer", name="delete_offer", methods={"DELETE"})
	 * @param Request $req
	 * @param TransportOffer $offer
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function     deleteOffer(Request $req, TransportOffer $offer,
		EntityManagerInterface $manager)
	{
		try
		{
			$this->findUserOrFail($req, true);
			$transport = $offer->getTransport();
			$transport->removeTransportOffer($offer);
			$manager->persist($transport);
			$manager->flush();
			return ($this->json("deleted", 200));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}
	/**
	 * @Route("/{id}", name="update_transport", methods={"PUT"})
	 * @param Request $req
	 * @param TransportMode $transport
	 */
	public function     update(Request $req, TransportMode $transport)
	{
		try
		{
			$this->findUserOrFail($req, true);
			$this->updateTransport($req->request->all(), $transport);
			return ($this->json("update"));
		}catch (UnauthorizedHttpException|AccessDeniedException $e)
		{
			return ($this->json($e->getMessage(), $e->getStatusCode()));
		}
	}

	private function    updateTransport($req, $transport)
	{
		$transport->setName($req['name']);
		$offers = $req['offers'];
		$c = -1;
		$offer  = $transport->getTransportOffers();
		$len = count($offer);
		while (++$c < $len)
		{
			$offer[$c]->setName($offers[$c]['name']);
			$specs = $offers[$c]['specs'];
			$spec = $offer[$c]->getSpecOffers();
			$c2 = -1;
			$len2 = count($spec);
			while (++$c2 < $len2)
			{
				$spec[$c2]->setName($specs[$c2]['name']);
				$spec[$c2]->setUnity($specs[$c2]['unity']);
				$spec[$c2]->setMinValue($specs[$c2]['minValue']);
				$spec[$c2]->setPrice( $specs[$c2]['price']);
			}
		}
		$manager = $this->getDoctrine()->getManager();
		$manager->persist($transport);
		$manager->flush();
		$manager->refresh($transport);
	}

	/**
	 * @Route("", name="create_transport_fee" ,methods={"POST"})
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
				, 201));
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
