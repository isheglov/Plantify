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
     * @ORM\Column(type="integer")
     */
    private $plantingMonth;

    /**
     * @ORM\Column(type="integer")
     */
    private $plantingSeedlingMonth;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class, inversedBy="plantings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlantingMonth(): ?int
    {
        return $this->plantingMonth;
    }

    public function setPlantingMonth(int $plantingMonth): self
    {
        $this->plantingMonth = $plantingMonth;

        return $this;
    }

    public function getPlantingSeedlingMonth(): ?int
    {
        return $this->plantingSeedlingMonth;
    }

    public function setPlantingSeedlingMonth(int $plantingSeedlingMonth): self
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
}
