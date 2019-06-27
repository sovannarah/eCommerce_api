<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransportModeRepository")
 */
class TransportMode
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
     * @ORM\OneToMany(targetEntity="App\Entity\TransportOffer", mappedBy="transport", cascade={"persist"})
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $transportOffer->setTransport($this);
        }

        return $this;
    }

    public function removeTransportOffer(TransportOffer $transportOffer): self
    {
        if ($this->transportOffers->contains($transportOffer)) {
            $this->transportOffers->removeElement($transportOffer);
            // set the owning side to null (unless already changed)
            if ($transportOffer->getTransport() === $this) {
                $transportOffer->setTransport(null);
            }
        }

        return $this;
    }
}
