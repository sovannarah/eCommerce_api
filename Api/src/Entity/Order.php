<?php


namespace App\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Order
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $receive;
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $send;
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $status;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;


	/**
	 * @param bool $status
	 * @return $this
	 */
	public function setStatus(bool $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getStatus(): ?bool
	{
		return $this->status;
	}

	/**
	 * @param \DateTimeInterface|null $receive
	 * @return $this
	 */
	public function setReceive(?\DateTimeInterface $receive): self
	{
		$this->receive = $receive;

		return $this;
	}

	public function getSend(): ?\DateTimeInterface
	{
		return $this->send;
	}

	/**
	 * @param \DateTimeInterface $send
	 * @return $this
	 */
	public function setSend(\DateTimeInterface $send): self
	{
		$this->send = $send;

		return $this;
	}

	public function getReceive(): ?\DateTimeInterface
	{
		return $this->receive;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return Collection|OrderItem[]
	 */
	abstract public function getOrderItems(): Collection;

	/**
	 * @param OrderItem $stockOrderItem
	 * @return $this
	 * @throws \InvalidArgumentException if $userOrderItem isn't of correct subtype
	 */
	protected function addOrderItem(OrderItem $stockOrderItem): self
	{
		if (!$this->getOrderItems()->contains($stockOrderItem)) {
			$this->getOrderItems()[] = $stockOrderItem;
			$stockOrderItem->setOrder($this);
		}

		return $this;
	}

	/**
	 * @param OrderItem $orderItem
	 * @return $this
	 */
	public function removeOrderItem(OrderItem $orderItem): self
	{
		if ($this->getOrderItems()->contains($orderItem)) {
			$this->getOrderItems()->removeElement($orderItem);
			// set the owning side to null (unless already changed)
			if ($orderItem->getOrder() === $this) {
				$orderItem->setOrder(null);
			}
		}

		return $this;
	}

	/**
	 * @ORM\PrePersist
	 */
	public function onPrePersistSetSend(): void
	{
		if (!$this->receive) {
			$this->receive = new \DateTime();
		}
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	/**
	 * @param User|null $user
	 * @return Order
	 * @throws UnauthorizedHttpException if $user is null
	 */
	public function setUser(?User $user): self
	{
		if (!$user) {
			throw new UnauthorizedHttpException('', 'User cannot be null');
		}
		$this->user = $user;

		return $this;
	}
}
