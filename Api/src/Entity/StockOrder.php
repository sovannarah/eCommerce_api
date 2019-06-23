<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockOrderRepository")
 */
class StockOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="stockOrder")
     */
    private $detail;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Send;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function __construct()
    {
        $this->detail = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Article[]
     */
    public function getDetail(): Collection
    {
        return $this->detail;
    }

    public function addDetail(Article $detail): self
    {
        if (!$this->detail->contains($detail)) {
            $this->detail[] = $detail;
            $detail->setStockOrder($this);
        }

        return $this;
    }

    public function removeDetail(Article $detail): self
    {
        if ($this->detail->contains($detail)) {
            $this->detail->removeElement($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getStockOrder() === $this) {
                $detail->setStockOrder(null);
            }
        }

        return $this;
    }

    public function getSend(): ?\DateTimeInterface
    {
        return $this->Send;
    }

    public function setSend(\DateTimeInterface $Send): self
    {
        $this->Send = $Send;

        return $this;
    }

    public function getRecive(): ?\DateTimeInterface
    {
        return $this->recive;
    }

    public function setRecive(?\DateTimeInterface $recive): self
    {
        $this->recive = $recive;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
