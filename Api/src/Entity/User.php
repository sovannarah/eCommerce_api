<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\Type;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @Assert\NotBlank
	 * @Assert\Email(
	 *     message = "The email '{{ value }}' is not a valid email.",
	 *     checkMX = true
	 * )
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="json")
	 * @Type("array")
	 */
	private $roles = [];

	/**
	 * @Assert\NotBlank
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="user", orphanRemoval=true)
	 */
	private $articles;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $token_expiration;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $token;

	/**
	 * @ORM\OneToMany(targetEntity="UserOrder", mappedBy="user")
	 */
	private $userOrders;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\StockOrder", mappedBy="user")
	 */
	private $stockOrders;

	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\Address", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $address;

	public function __construct()
	{
		$this->articles = new ArrayCollection();
		$this->userOrders = new ArrayCollection();
		$this->stockOrders = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUsername(): string
	{
		return (string)$this->email;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
	{
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';

		return ($roles);
	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getPassword(): string
	{
		return (string)$this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getSalt()
	{
		// not needed when using the "bcrypt" algorithm in security.yaml
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
	{
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
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
			$article->setUser($this);
		}

		return $this;
	}

	public function removeArticle(Article $article): self
	{
		if ($this->articles->contains($article)) {
			$this->articles->removeElement($article);
			// set the owning side to null (unless already changed)
			if ($article->getUser() === $this) {
				$article->setUser(null);
			}
		}

		return $this;
	}

	public function getTokenExpiration(): ?\DateTimeInterface
	{
		return $this->token_expiration;
	}

	public function setTokenExpiration(?\DateTimeInterface $token_expiration): self
	{
		$this->token_expiration = $token_expiration;

		return $this;
	}

	public function getToken(): ?string
	{
		return $this->token;
	}

	public function setToken(?string $token): self
	{
		$this->token = $token;

		return $this;
	}

	/**
	 * @return Collection|AbstractOrder[]
	 */
	public function getUserOrders(): Collection
	{
		return $this->userOrders;
	}

	public function addUserOrder(AbstractOrder $order): self
	{
		if (!$this->userOrders->contains($order)) {
			$this->userOrders[] = $order;
			$order->setUser($this);
		}

		return $this;
	}

	public function removeUserOrder(AbstractOrder $order): self
	{
		if ($this->userOrders->contains($order)) {
			$this->userOrders->removeElement($order);
			// set the owning side to null (unless already changed)
			if ($order->getUser() === $this) {
				$order->setUser(null);
			}
		}

		return $this;
	}

	public function isAdmin(): bool
	{
		return in_array('ROLE_ADMIN', $this->getRoles(), true);
	}

	/**
	 * @return Collection|StockOrder[]
	 */
	public function getStockOrders(): Collection
	{
		return $this->stockOrders;
	}

	public function addStockOrder(StockOrder $stockOrder): self
	{
		if (!$this->stockOrders->contains($stockOrder)) {
			$this->stockOrders[] = $stockOrder;
			$stockOrder->setUser($this);
		}

		return $this;
	}

	public function removeStockOrder(StockOrder $stockOrder): self
	{
		if ($this->stockOrders->contains($stockOrder)) {
			$this->stockOrders->removeElement($stockOrder);
			// set the owning side to null (unless already changed)
			if ($stockOrder->getUser() === $this) {
				$stockOrder->setUser(null);
			}
		}

		return $this;
	}

	public function getAddress(): ?Address
	{
		return $this->address;
	}

	public function setAddress(?Address $address): self
	{
		$this->address = $address;

		// set (or unset) the owning side of the relation if necessary
		if ($address !== null) {
			$curUser = $address->getUser();
			if (!$curUser || $curUser->getId() !== $this->getId()) {
				$address->setUser($this);``
			}
		}

		return $this;
	}
}
