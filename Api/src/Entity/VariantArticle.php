<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VariantArticleRepository")
 */
class VariantArticle implements \JsonSerializable
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Article", cascade={"persist"}, inversedBy="variantArticles")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $parent;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $spec;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $varPrice;

	/**
	 * @ORM\Column(type="string")
	 */
	private $type;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getParent(): ?Article
	{
		return $this->parent;
	}

	public function setParent(?Article $parent): self
	{
		$this->parent = $parent;

		return $this;
	}

	public function getSpec(): ?string
	{
		return $this->spec;
	}

	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}
	public function getType(): ?string
	{
		return $this->type;
	}

	public function setSpec(string $spec): self
	{
		$this->spec = $spec;

		return $this;
	}

	public function getVarPrice(): ?int
	{
		return $this->varPrice;
	}

	public function setVarPrice(int $varPrice): self
	{
		$this->varPrice = $varPrice;

		return $this;
	}
	
	/**
	 * Specify data which should be serialized to JSON
	 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'spec' => $this->getSpec(),
			'var_price' => $this->getVarPrice(),
			'type' => $this->getType()
		];
	}
}
