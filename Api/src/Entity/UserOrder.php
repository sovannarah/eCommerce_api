<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserOrderRepository")
 */
class UserOrder extends AbstractOrder
{
	/**
	 * @ORM\OneToMany(targetEntity="UserOrderItem", mappedBy="userOrder", orphanRemoval=true)
	 */
	private $orderItems;

	public function __construct()
	{
		$this->orderItems = new ArrayCollection();
	}

	/**
	 * @return Collection|AbstractOrderItem[]
	 */
	public function getOrderItems(): Collection
	{
		return $this->orderItems;
	}

	/**
	 * @param UserOrderItem|AbstractOrderItem $userOrderItem
	 * @return $this
	 * @throws \InvalidArgumentException if $orderItems isn't UserOrderItem
	 */
	public function addOrderItem(AbstractOrderItem $userOrderItem): AbstractOrder
	{
		if (!$userOrderItem instanceof UserOrderItem) {
			throw new \InvalidArgumentException('Param $userOrderItem must be '.UserOrderItem::class);
		}
		return parent::addOrderItem($userOrderItem);
	}
}
