<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=GardenCell::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $cell;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\Column(type="date")
     */
    private $plantedFrom;

    /**
     * @ORM\Column(type="date")
     */
    private $plantedTo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCell(): ?GardenCell
    {
        return $this->cell;
    }

    public function setCell(?GardenCell $cell): self
    {
        $this->cell = $cell;

        return $this;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }

    public function getPlantedFrom(): ?\DateTimeInterface
    {
        return $this->plantedFrom;
    }

    public function setPlantedFrom(\DateTimeInterface $plantedFrom): self
    {
        $this->plantedFrom = $plantedFrom;

        return $this;
    }

    public function getPlantedTo(): ?\DateTimeInterface
    {
        return $this->plantedTo;
    }

    public function setPlantedTo(\DateTimeInterface $plantedTo): self
    {
        $this->plantedTo = $plantedTo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
