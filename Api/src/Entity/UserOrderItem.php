<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserOrderItemRepository")
 */
class UserOrderItem extends OrderItem
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\UserOrder", inversedBy="orderItems")
	 */
	private $userOrder;

	/**
	 * @return UserOrder|null
	 */
	public function getOrder(): ?Order
	{
		return $this->userOrder;
	}

	/**
	 * @param UserOrder|Order|null $userOrder
	 * @return $this
	 * @throws \InvalidArgumentException if $userOrder isn't StockOrder
	 */
	public function setOrder(?Order $userOrder): OrderItem
	{
		if (!$userOrder instanceof UserOrder) {
			throw new \InvalidArgumentException('Parameter $userOrder to be of type '.UserOrder::class);
		}
		$this->userOrder = $userOrder;

		return $this;
	}

}
