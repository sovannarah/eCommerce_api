<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserOrderItemRepository")
 */
class UserOrderItem extends AbstractOrderItem
{
	/**
	 * @ORM\ManyToOne(targetEntity="UserAbstractOrder.php", inversedBy="orderItems")
	 */
	private $userOrder;

	/**
	 * @return UserOrder|null
	 */
	public function getOrder(): ?AbstractOrder
	{
		return $this->userOrder;
	}

	/**
	 * @param UserOrder|AbstractOrder|null $userOrder
	 * @return $this
	 * @throws \InvalidArgumentException if $userOrder isn't StockOrder
	 */
	public function setOrder(?AbstractOrder $userOrder): AbstractOrderItem
	{
		if (!$userOrder instanceof UserOrder) {
			throw new \InvalidArgumentException('Parameter $userOrder to be of type '.UserOrder::class);
		}
		$this->userOrder = $userOrder;

		return $this;
	}

}
