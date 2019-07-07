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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="stockOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

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
	 * @throws UnauthorizedHttpException if $user is null
	 * @throws AccessDeniedHttpException if $user is not admin
	 */
	public function setUser(?User $user): AbstractOrder
	{

		if (!$user) {
			throw new UnauthorizedHttpException('', 'User cannot be null');
		}
		if (!$user->isAdmin()) {
			throw new AccessDeniedHttpException('User must be admin');
		}
		$this->user = $user;

		return $this;
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
