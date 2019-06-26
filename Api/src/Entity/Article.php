<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article implements \JsonSerializable
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank
	 */
	private $title;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @ORM\Column(type="integer", options={"unsigned":true})
	 * @Assert\PositiveOrZero
	 * @Assert\GreaterThanOrEqual(0)
	 */
	private $price;

	/**
	 * @ORM\Column(type="json")
	 * @Assert\All({@Assert\Image})
	 */
	private $images = [];

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="articles")
	 * @ORM\JoinColumn(nullable=false)
	 * @Assert\NotNull
	 */
	private $category;

	/**
	 * @ORM\Column(type="integer", options={"default":0, "unsigned":true})
	 * @Assert\PositiveOrZero
	 * @Assert\GreaterThanOrEqual(0)
	 */
	private $nb_views = 0;

	/**
	 * @ORM\Column(type="integer", options={"unsigned":true})
	 * @Assert\PositiveOrZero
	 * @Assert\GreaterThanOrEqual(0)
	 */
	private $stock;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="article")
	 */
	private $orderItems;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\VariantArticle", mappedBy="parent", orphanRemoval=true)
	 */
	private $variantArticles;

	public function __construct()
         	{
         		$this->orderItems = new ArrayCollection();
         		$this->variants = new ArrayCollection();
         		$this->variantArticles = new ArrayCollection();
         	}
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="variants")
	 */
	private $variantOf;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="variantOf")
	 */
	private $variants;

    /**
     * @ORM\Column(type="integer")
     */
    private $kg;

	public function getId(): ?int
         	{
         		return $this->id;
         	}

	public function getUser(): ?User
         	{
         		return $this->user;
         	}

	/**
	 * @param User|null $user
	 * @return $this
	 * @throws InvalidParameterException if $user is null
	 */
	public function setUser(?User $user): self
         	{
         		static::_assertNotNull('user', $user);
         		$this->user = $user;
         
         		return $this;
         	}

	public function getTitle(): ?string
         	{
         		return $this->title;
         	}

	/**
	 * @param string|null $title
	 * @return $this
	 * @throws InvalidParameterException if $title is null
	 */
	public function setTitle(?string $title): self
         	{
         		static::_assertString('title', $title);
         		$this->title = $title;
         
         		return $this;
         	}

	public function getDescription(): ?string
         	{
         		return $this->description;
         	}

	/**
	 * @param string|null $description
	 * @return $this
	 * @throws InvalidParameterException if $description is null
	 */
	public function setDescription(?string $description): self
         	{
         		static::_assertString('description', $description);
         		$this->description = $description;
         
         		return $this;
         	}

	public function getPrice(): ?int
         	{
         		return $this->price;
         	}

	/**
	 * @Assert\PositiveOrZero
	 * @param int|null $price >= 0
	 * @return $this
	 * @throws InvalidParameterException if $price is negative ot null
	 */
	public function setPrice($price = null): self
         	{
         		static::_assertNotNegInt('price', $price);
         		$this->price = $price;
         
         		return $this;
         	}

	public function getImages(): array
         	{
         		return $this->images ?? [];
         	}

	public function setImages($images = []): self
         	{
         		if (!is_array($images)) {
         			throw new InvalidParameterException('images must be an array');
         		}
         		$this->images = $images;
         
         		return $this;
         	}


	public function getCategory(): ?Category
         	{
         		return $this->category;
         	}

	/**
	 * @param Category|null $category
	 * @return $this
	 * @throws InvalidParameterException if $category is null
	 */
	public function setCategory(?Category $category): self
         	{
         		static::_assertNotNull('category', $category);
         		$this->category = $category;
         
         		return $this;
         	}

	public function getNbViews(): int
         	{
         		return $this->nb_views ?? 0;
         	}

	public function setNbViews($nb_views): self
         	{
         		if ($nb_views === null) {
         			$nb_views = 0;
         		} else {
         			static::_assertNotNegInt('nb_views', $nb_views);
         		}
         		$this->nb_views = (int)$nb_views;
         
         		return $this;
         	}

	public function incrementNbViews(): self
         	{
         		return $this->setNbViews($this->getNbViews() + 1);
         	}

	public function getStock(): ?int
         	{
         		return $this->stock;
         	}

	/**
	 * @param int|null $stock
	 * @return $this
	 * @throws InvalidParameterException if $stock is not null, or positive or zero int
	 */
	public function setStock($stock): self
         	{
         		$stock = $stock !== '' ? $stock : null;
         		static::_assertNotNegInt('stock', $stock, true);
         		$this->stock = $stock;
         
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
         		$simpleSerializable = $this->nestedJsonSerialize();
         		$simpleSerializable['category'] =
         			Category::rec_jsonSerializeParent($this->getCategory());
         		$simpleSerializable['variants'] = $this->orderVariants();
         
         		return $simpleSerializable;
         	}

	public function nestedJsonSerialize(): array
         	{
         		return [
         			'id' => $this->getId(),
         			'title' => $this->getTitle(),
         			'description' => $this->getDescription(),
         			'price' => $this->getPrice(),
         			'nb_views' => $this->getNbViews(),
         			'stock' => $this->getStock(),
         			'images' => $this->_jsonSerializeImages(),
         		];
         	}

	private function orderVariants(): array
         	{
         		$array = [];
         		foreach ($this->getVariantArticles() as $variant)
         			$array[$variant->getType()][] = $variant;
         		return $array;
         	}

	private function _jsonSerializeImages(): array
         	{
         		$imageNames = [];
         		foreach ($this->getImages() as $image) {
         			$imageNames[] = ($image instanceof \SplFileInfo) ?
         				$image->getFilename() :
         				$image;
         		}
         
         		return $imageNames;
         	}

	/**
	 * @param string $fieldName
	 * @param mixed $val
	 * @param bool $allowNull
	 */
	private static function _assertNotNegInt(
         		string $fieldName,
         		$val,
         		bool $allowNull = false
         	): void {
         		if (($allowNull && $val === null)
         			|| (is_int($val) && $val >= 0)
         			|| ctype_digit($val)
         		) {
         			return;
         		}
         		throw new InvalidParameterException(
         			$fieldName.' must be a positive or 0 int'
         		);
         	}

	private static function _assertNotNull(string $fieldName, $val): void
         	{
         		if ($val === null) {
         			throw new InvalidParameterException(
         				$fieldName.' must not be null'
         			);
         		}
         	}

	private static function _assertString(
         	string $fieldName,
         	$val,
         	bool $allowEmpty = false,
         	bool $allowNull = true
         	): void {
         		if (($allowNull && $val === null)
         			|| ($allowEmpty && $val === '')
         			|| is_string($val)) {
         			return;
         		}
         		throw new InvalidParameterException($fieldName.' invalid');
         	}

	public function getStockOrder(): ?StockOrder
         	{
         		return $this->stockOrder;
         	}

	public function setStockOrder(?StockOrder $stockOrder): self
         	{
         		$this->stockOrder = $stockOrder;
         
         		return $this;
         	}
	public function getVariantOf(): ?self
         	{
         		return $this->variantOf;
         	}

	public function setVariantOf(?self $variantOf): self
         	{
         		$this->variantOf = $variantOf;
         
         		return $this;
         	}

	/**
	 * @return Collection|self[]
	 */
	public function getVariants(): Collection
         	{
         		return $this->variants;
         	}

	public function addVariant(self $variant): self
         	{
         		if (!$this->variants->contains($variant)) {
         			$this->variants[] = $variant;
         			$variant->setVariantOf($this);
         		}
         
         		return $this;
         	}

	public function removeVariant(self $variant): self
         	{
         		if ($this->variants->contains($variant)) {
         			$this->variants->removeElement($variant);
         			// set the owning side to null (unless already changed)
         			if ($variant->getVariantOf() === $this) {
         				$variant->setVariantOf(null);
         			}
         		}
         
         		return $this;
         	}

	/**
	 * @return Collection|VariantArticle[]
	 */
	public function getVariantArticles(): Collection
         	{
         		return $this->variantArticles;
         	}

	public function addVariantArticle(VariantArticle $variantArticle): self
         	{
         		if (!$this->variantArticles->contains($variantArticle)) {
         			$this->variantArticles[] = $variantArticle;
         			$variantArticle->setParent($this);
         		}
         
         		return $this;
         	}

	public function removeVariantArticle(VariantArticle $variantArticle): self
         	{
         		if ($this->variantArticles->contains($variantArticle)) {
         			$this->variantArticles->removeElement($variantArticle);
         			// set the owning side to null (unless already changed)
         			if ($variantArticle->getParent() === $this) {
         				$variantArticle->setParent(null);
         			}
         		}
         
         		return $this;
         	}

    public function getKg(): ?int
    {
        return $this->kg;
    }

    public function setKg(int $kg): self
    {
        $this->kg = $kg;

        return $this;
    }
}
