<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserOrderRepository")
 */
class UserOrder extends AbstractOrder
{
	/**
	 * @ORM\OneToMany(targetEntity="UserOrderItem", mappedBy="userOrder", orphanRemoval=true)
	 */
	private $orderItems;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $user;

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
	/**
	 * @param User|null $user
	 * @return $this
	 */
	public function setUser(?User $user): AbstractOrder
      	{
      
      		if (!$user) {
      			throw new UnauthorizedHttpException('', 'User cannot be null');
      		}
      		$this->user = $user;
      		return $this;
      	}

	public function getUser(): ?User
         	{
         		return $this->user;
         	}
}
