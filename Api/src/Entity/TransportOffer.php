<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransportOfferRepository")
 */
class TransportOffer
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
     * @ORM\ManyToOne(targetEntity="App\Entity\TransportFee", inversedBy="transportOffers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $transportFee;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SpecsOffer", mappedBy="offer")
     */
    private $specsOffers;

    public function __construct()
    {
        $this->specsOffers = new ArrayCollection();
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

    public function getTransportFee(): ?TransportFee
    {
        return $this->transportFee;
    }

    public function setTransportFee(?TransportFee $transportFee): self
    {
        $this->transportFee = $transportFee;

        return $this;
    }

    /**
     * @return Collection|SpecsOffer[]
     */
    public function getSpecsOffers(): Collection
    {
        return $this->specsOffers;
    }

    public function addSpecsOffer(SpecsOffer $specsOffer): self
    {
        if (!$this->specsOffers->contains($specsOffer)) {
            $this->specsOffers[] = $specsOffer;
            $specsOffer->setOffer($this);
        }

        return $this;
    }

    public function removeSpecsOffer(SpecsOffer $specsOffer): self
    {
        if ($this->specsOffers->contains($specsOffer)) {
            $this->specsOffers->removeElement($specsOffer);
            // set the owning side to null (unless already changed)
            if ($specsOffer->getOffer() === $this) {
                $specsOffer->setOffer(null);
            }
        }

        return $this;
    }
}
