<?php

namespace App\Entity;

use App\Repository\PlantingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlantingRepository::class)
 */
class Planting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $plantingMonth;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $plantingSeedlingMonth;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, inversedBy="plantings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlantingMonth(): ?string
    {
        return $this->plantingMonth;
    }

    public function setPlantingMonth(string $plantingMonth): self
    {
        $this->plantingMonth = $plantingMonth;

        return $this;
    }

    public function getPlantingSeedlingMonth(): ?string
    {
        return $this->plantingSeedlingMonth;
    }

    public function setPlantingSeedlingMonth(string $plantingSeedlingMonth): self
    {
        $this->plantingSeedlingMonth = $plantingSeedlingMonth;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function __toString(): string
    {
        return $this->region->getName() . '_' . $this->getPlantingMonth();
    }
}
