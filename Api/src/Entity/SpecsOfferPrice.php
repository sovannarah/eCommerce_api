<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpecsOfferPriceRepository")
 */
class SpecsOfferPrice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SpecsOffer", inversedBy="specsOfferPrices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $specoffer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSpecoffer(): ?SpecsOffer
    {
        return $this->specoffer;
    }

    public function setSpecoffer(?SpecsOffer $specoffer): self
    {
        $this->specoffer = $specoffer;

        return $this;
    }
}
