<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransportFeeRepository")
 */
class TransportFee
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
    private $namw;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TransportOffer", mappedBy="transportFee")
     */
    private $transportOffers;

    public function __construct()
    {
        $this->transportOffers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamw(): ?string
    {
        return $this->namw;
    }

    public function setNamw(string $namw): self
    {
        $this->namw = $namw;

        return $this;
    }

    /**
     * @return Collection|TransportOffer[]
     */
    public function getTransportOffers(): Collection
    {
        return $this->transportOffers;
    }

    public function addTransportOffer(TransportOffer $transportOffer): self
    {
        if (!$this->transportOffers->contains($transportOffer)) {
            $this->transportOffers[] = $transportOffer;
            $transportOffer->setTransportFee($this);
        }

        return $this;
    }

    public function removeTransportOffer(TransportOffer $transportOffer): self
    {
        if ($this->transportOffers->contains($transportOffer)) {
            $this->transportOffers->removeElement($transportOffer);
            // set the owning side to null (unless already changed)
            if ($transportOffer->getTransportFee() === $this) {
                $transportOffer->setTransportFee(null);
            }
        }

        return $this;
    }
}
