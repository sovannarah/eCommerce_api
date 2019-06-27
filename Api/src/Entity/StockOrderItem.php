<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockOrderItemRepository")
 */
class StockOrderItem extends AbstractOrderItem
{
	/**
	 * @ORM\ManyToOne(targetEntity="StockOrder", inversedBy="orderItems")
	 */
    private $stockOrder;

	/**
	 * @return StockOrder|null
	 */
	public function getOrder(): ?AbstractOrder
	{
		return $this->stockOrder;
	}

	/**
	 * @param StockOrder|AbstractOrder|null $stockOrder
	 * @return $this
	 * @throws \InvalidArgumentException if $order isn't StockOrder
	 */
	public function setOrder(?AbstractOrder $stockOrder): AbstractOrderItem
	{
		if (!$stockOrder instanceof StockOrder) {
			throw new \InvalidArgumentException('Parameter $stockOrder to be of type '. StockOrder::class);
		}
		$this->stockOrder = $stockOrder;

		return $this;
	}

}
