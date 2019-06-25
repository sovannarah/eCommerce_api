<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserOrderRepository")
 */
class UserOrder extends Order
{
	/**
	 * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="orders", orphanRemoval=true)
	 */
	private $orderItems;

	public function __construct()
	{
		$this->orderItems = new ArrayCollection();
	}

	/**
	 * @return Collection|OrderItem[]
	 */
	public function getOrderItems(): Collection
	{
		return $this->orderItems;
	}


}
