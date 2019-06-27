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
     * @ORM\ManyToOne(targetEntity="App\Entity\TransportMode", inversedBy="transportOffers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $transport;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SpecOffer", mappedBy="offer", orphanRemoval=true, cascade={"persist"})
     */
    private $specOffers;

    public function __construct()
    {
        $this->specOffers = new ArrayCollection();
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

    public function getTransport(): ?TransportMode
    {
        return $this->transport;
    }

    public function setTransport(?TransportMode $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * @return Collection|SpecOffer[]
     */
    public function getSpecOffers(): Collection
    {
        return $this->specOffers;
    }

    public function addSpecOffer(SpecOffer $specOffer): self
    {
        if (!$this->specOffers->contains($specOffer)) {
            $this->specOffers[] = $specOffer;
            $specOffer->setOffer($this);
        }

        return $this;
    }

    public function removeSpecOffer(SpecOffer $specOffer): self
    {
        if ($this->specOffers->contains($specOffer)) {
            $this->specOffers->removeElement($specOffer);
            // set the owning side to null (unless already changed)
            if ($specOffer->getOffer() === $this) {
                $specOffer->setOffer(null);
            }
        }

        return $this;
    }

}
