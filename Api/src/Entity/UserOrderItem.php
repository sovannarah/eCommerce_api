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
	 * @param UserOrder|Order|null $order
	 * @return $this
	 * @throws \InvalidArgumentException if $order isn't StockOrder
	 */
	public function setOrder(?Order $order): OrderItem
	{
		if (!$order instanceof UserOrder) {
			throw new \InvalidArgumentException('Parameter $order to be of type '.UserOrder::class);
		}
		$this->userOrder = $order;

		return $this;
	}

}