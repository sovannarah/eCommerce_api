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
	 * @param StockOrder|Order|null $stockOrder
	 * @return $this
	 * @throws \InvalidArgumentException if $order isn't StockOrder
	 */
	public function setOrder(?Order $stockOrder): OrderItem
	{
		if (!$stockOrder instanceof StockOrder) {
			throw new \InvalidArgumentException('Parameter $stockOrder to be of type '. StockOrder::class);
		}
		$this->stockOrder = $stockOrder;

		return $this;
	}

}
