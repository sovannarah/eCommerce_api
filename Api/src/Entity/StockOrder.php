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
class StockOrder extends AbstractOrder
{
	/**
	 * @ORM\OneToMany(targetEntity="StockOrderItem", mappedBy="stockOrder", orphanRemoval=true)
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
	 * @param User|null $user
	 * @return $this
	 * @throws UnauthorizedHttpException
	 * @throws AccessDeniedHttpException if $user is not admin
	 */
	public function setUser(?User $user): AbstractOrder
	{
		if ($user && !$user->isAdmin()) {
			throw new AccessDeniedHttpException('User must be admin');
		}
		return parent::setUser($user);
	}

	/**
	 * @param StockOrderItem|AbstractOrderItem $stockOrderItem
	 * @return $this
	 * @throws \InvalidArgumentException if $orderItems isn't StockOrderItem
	 */
	public function addOrderItem(AbstractOrderItem $stockOrderItem): AbstractOrder
	{
		if (!$stockOrderItem instanceof StockOrderItem) {
			throw new \InvalidArgumentException('Param $stockOrderItem must be '.StockOrderItem::class);
		}
		return parent::addOrderItem($stockOrderItem);
	}
}
