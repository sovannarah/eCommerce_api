<?php


namespace App\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractOrder implements \JsonSerializable
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
	 * @return Collection|AbstractOrderItem[]
	 */
	abstract public function getOrderItems(): Collection;

	/**
	 * @param AbstractOrderItem $stockOrderItem
	 * @return $this
	 * @throws \InvalidArgumentException if $userOrderItem isn't of correct subtype
	 */
	public function addOrderItem(AbstractOrderItem $stockOrderItem): self
	{
		if (!$this->getOrderItems()->contains($stockOrderItem)) {
			$this->getOrderItems()[] = $stockOrderItem;
			$stockOrderItem->setOrder($this);
		}

		return $this;
	}

	/**
	 * @param AbstractOrderItem $orderItem
	 * @return $this
	 */
	public function removeOrderItem(AbstractOrderItem $orderItem): self
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
		if (!$this->send) {
			$this->send = new \DateTime();
		}
	}

	public function getUser(): ?User
	{
		return $this->user ?? null;
	}

	/**
	 * @param User|null $user
	 * @return $this
	 */
	abstract public function setUser(?User $user): self;

	public function jsonSerialize()
	{
		$jsonAble = [
			'userId' => $this->getUser()->getId(),
			'items' => $this->getOrderItems()->toArray(),
		];
		foreach (['id', 'send', 'receive'] as $item) {
			$jsonAble[$item] = $this->{'get'.ucfirst($item)}();
		}
		return $jsonAble;
	}
}
