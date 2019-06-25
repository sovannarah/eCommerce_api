<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpecsOfferRepository")
 */
class SpecsOffer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $unity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TransportOffer", inversedBy="specsOffers")
     */
    private $offer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SpecsOfferPrice", mappedBy="specoffer")
     */
    private $specsOfferPrices;

    public function __construct()
    {
        $this->specsOfferPrices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getOffer(): ?TransportOffer
    {
        return $this->offer;
    }

    public function setOffer(?TransportOffer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * @return Collection|SpecsOfferPrice[]
     */
    public function getSpecsOfferPrices(): Collection
    {
        return $this->specsOfferPrices;
    }

    public function addSpecsOfferPrice(SpecsOfferPrice $specsOfferPrice): self
    {
        if (!$this->specsOfferPrices->contains($specsOfferPrice)) {
            $this->specsOfferPrices[] = $specsOfferPrice;
            $specsOfferPrice->setSpecoffer($this);
        }

        return $this;
    }

    public function removeSpecsOfferPrice(SpecsOfferPrice $specsOfferPrice): self
    {
        if ($this->specsOfferPrices->contains($specsOfferPrice)) {
            $this->specsOfferPrices->removeElement($specsOfferPrice);
            // set the owning side to null (unless already changed)
            if ($specsOfferPrice->getSpecoffer() === $this) {
                $specsOfferPrice->setSpecoffer(null);
            }
        }

        return $this;
    }
}
