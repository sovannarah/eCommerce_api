<?php

namespace App\Entity;

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
     * @ORM\Column(type="integer", options={"unsigned"=true, })
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
     * @ORM\Column(type="integer", options={"default":0})
     * @Assert\PositiveOrZero
     * @Assert\GreaterThanOrEqual(0)
     */
    private $nb_views = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     * @Assert\GreaterThanOrEqual(0)
     */
    private $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @Assert\PositiveOrZero
     * @param int $price >= 0
     * @return Article
     * @throws InvalidParameterException if $price is negative
     */
    public function setPrice(int $price): self
    {
        self::_assertNotNeg($price);
        $this->price = $price;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images ?? [];
    }

    public function setImages(array $images = []): self
    {
        $this->images = $images;

        return $this;
    }


    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getNbViews(): int
    {
        return $this->nb_views ?? 0;
    }

    public function setNbViews(int $nb_views = 0): self
    {
        self::_assertNotNeg($nb_views);
        $this->nb_views = $nb_views;

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

    public function setStock(?int $stock): self
    {
        if ($stock !== null) {
            self::_assertNotNeg($stock);
        }
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
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'nb_views' => $this->getNbViews(),
            'stock' => $this->getStock(),
            'images' => array_map(
                static function ($image) {
                    return ($image instanceof \SplFileInfo) ?
                        $image->getFilename() :
                        $image;
                },
                $this->getImages()
            ),
            'category' => self::_rec_jsonSerializeCategory($this->getCategory()),
        ];
    }

	private static function _rec_jsonSerializeCategory(Category $category = null): ?array
	{
		return !$category ?
			null :
			[
				'id' => $category->getId(),
				'name' => $category->getName(),
				'parent' => self::_rec_jsonSerializeCategory($category->getParent()),
			];
	}

	/**
	 * @param int $price
	 * @throws InvalidParameterException if $price < 0
	 */
	private static function _assertNotNeg(int $price): void
	{
		if ($price < 0) {
			throw new InvalidParameterException('price must not be negative');
		}
	}
}
