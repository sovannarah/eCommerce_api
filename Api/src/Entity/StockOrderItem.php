<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockOrderItemRepository")
 */
class StockOrderItem extends OrderItem
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\StockOrder", inversedBy="orderItems")
	 */
    private $stockOrder;

	/**
	 * @return StockOrder|null
	 */
	public function getOrder(): ?Order
	{
		return $this->stockOrder;
	}

	/**
	 * @param StockOrder|Order|null $order
	 * @return $this
	 * @throws \InvalidArgumentException if $order isn't StockOrder
	 */
	public function setOrder(?Order $order): OrderItem
	{
		if (!$order instanceof StockOrder) {
			throw new \InvalidArgumentException(__METHOD__ . ' requires parameter order to be of type '. StockOrder::class);
		}
		$this->stockOrder = $order;

		return $this;
	}

}
