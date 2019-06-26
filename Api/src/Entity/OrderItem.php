<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 */
abstract class OrderItem
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="orderItems")
	 */
	private $article;

	/**
	 * @ORM\Column(type="integer", options={"unsigned":true})
	 * @Assert\PositiveOrZero
	 * @Assert\GreaterThanOrEqual(0)
	 */
	private $quantity;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getArticle(): ?Article
	{
		return $this->article;
	}

	/**
	 * @param Article|null $article
	 * @return $this
	 */
	public function setArticle(?Article $article): self
	{
		$this->article = $article;

		return $this;
	}

	public function getQuantity(): ?int
	{
		return $this->quantity;
	}

	/**
	 * @param int $quantity
	 * @return $this
	 */
	public function setQuantity(int $quantity): self
	{
		$this->quantity = $quantity;

		return $this;
	}

	abstract public function getOrder(): ?Order;

	/**
	 * @param Order|null $order
	 * @return $this
	 * @throws \InvalidArgumentException if $order isn't of correct subtype
	 */
	abstract public function setOrder(?Order $order): self;
}
