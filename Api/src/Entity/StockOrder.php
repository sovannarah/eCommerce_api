<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockOrderRepository")
 */
class StockOrder extends Order
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

	/**
	 * @param User|null $user
	 * @return Order
	 * @throws UnauthorizedHttpException | AccessDeniedHttpException
	 */
	public function setUser(?User $user): Order
	{
		if ($user && !$user->isAdmin()) {
			throw new AccessDeniedHttpException('User must be admin');
		}
		return parent::setUser($user);
	}

	/**
	 * @param StockOrderItem|OrderItem $stockOrderItem
	 * @return $this
	 * @throws \InvalidArgumentException if $orderItems isn't StockOrderItem
	 */
	public function addOrderItem(OrderItem $stockOrderItem): Order
	{
		if (!$stockOrderItem instanceof StockOrderItem) {
			throw new \InvalidArgumentException('Param $stockOrderItem must be '.StockOrderItem::class);
		}
		return parent::addOrderItem($stockOrderItem);
	}
}
