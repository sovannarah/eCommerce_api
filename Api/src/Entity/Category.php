<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category implements \JsonSerializable
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="category", orphanRemoval=true)
	 */
	private $articles;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="children")
	 */
	private $parent;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
	 */
	private $children;

	public function __construct()
	{
		$this->articles = new ArrayCollection();
		$this->children = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return Collection|Article[]
	 */
	public function getArticles(): Collection
	{
		return $this->articles;
	}

	public function addArticle(Article $article): self
	{
		if (!$this->articles->contains($article)) {
			$this->articles[] = $article;
			$article->setCategory($this);
		}

		return $this;
	}

	public function removeArticle(Article $article): self
	{
		if ($this->articles->contains($article)) {
			$this->articles->removeElement($article);
			// set the owning side to null (unless already changed)
			if ($article->getCategory() === $this) {
				$article->setCategory(null);
			}
		}

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getParent(): ?self
	{
		return $this->parent;
	}

	public function setParent(?self $parent): self
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * @return Collection|self[]
	 */
	public function getChildren(): Collection
	{
		return $this->children;
	}

	public function addChild(self $child): self
	{
		if (!$this->children->contains($child)) {
			$this->children[] = $child;
			$child->setParent($this);
		}

		return $this;
	}

	public function removeChild(self $child): self
	{
		if ($this->children->contains($child)) {
			$this->children->removeElement($child);
			// set the owning side to null (unless already changed)
			if ($child->getParent() === $this) {
				$child->setParent(null);
			}
		}

		return $this;
	}

	public function isDeepParentOf(Category $child): bool
	{
		if ($this->getChildren()->isEmpty()) {
			return false;
		}
		while($child = $child->getParent()) {
			if ($this->id === $child->getId()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 *
	 * @uses Category::_jsonSerializeChildren()
	 * @uses Category::_rec_jsonSerializeParent()
	 *
	 */
	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'children' => $this->_jsonSerializeChildren(),
			'parent' => self::rec_jsonSerializeParent($this->getParent()),
		];
	}

	/**
	 * converts all $children Categories to json serializable data,
	 *
	 * @return array
	 * @see Category::jsonSerialize()
	 */
	private function _jsonSerializeChildren(): array
	{
		$serializableChildren = [];
		foreach ($this->getChildren() as $child) {
			$serializableChildren[] = [
				'id' => $child->getId(),
				'name' => $child->getName(),
			];
		}

		return $serializableChildren;
	}

	public function rec_nestedJsonSerialize(): array
	{
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'sub' => array_map(
				static function (Category $category) {
					return $category->rec_nestedJsonSerialize();
				},
				$this->getChildren()->toArray()
			),
		];
	}

	public function     getDeepChildrenId(array &$tChildIds)
	{
		$tChildIds[] = $this->getId();
		$children = $this->getChildren();
		$c = -1;
		$lenchild = $children->count();
		while (++$c < $lenchild)
			$children[$c]->getDeepChildrenId($tChildIds);
	}

	/**
	 * Recursively converts given parent category to json serializable data,
	 *   consisting only of id, name, and recursive parent
	 *
	 * @param Category $parent
	 * @return array
	 * @see Category::jsonSerialize()
	 */
	public static function rec_jsonSerializeParent(Category $parent = null): ?array
	{
		return !$parent ?
			null :
			[
				'id' => $parent->getId(),
				'name' => $parent->getName(),
				'parent' => self::rec_jsonSerializeParent($parent->getParent()),
			];
	}


}
