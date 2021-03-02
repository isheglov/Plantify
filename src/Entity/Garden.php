<?php

namespace App\Entity;

use App\Repository\GardenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GardenRepository::class)
 */
class Garden
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
    private $dimensionX;

    /**
     * @ORM\Column(type="integer")
     */
    private $dimensionY;

    /**
     * @ORM\OneToMany(targetEntity=GardenCell::class, mappedBy="garden", orphanRemoval=true)
     */
    private $cellList;

    public function __construct()
    {
        $this->cellList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDimensionX(): ?int
    {
        return $this->dimensionX;
    }

    public function setDimensionX(int $dimensionX): self
    {
        $this->dimensionX = $dimensionX;

        return $this;
    }

    public function getDimensionY(): ?int
    {
        return $this->dimensionY;
    }

    public function setDimensionY(int $dimensionY): self
    {
        $this->dimensionY = $dimensionY;

        return $this;
    }

    /**
     * @return Collection|GardenCell[]
     */
    public function getCellList(): Collection
    {
        return $this->cellList;
    }

    public function addCellList(GardenCell $cellList): self
    {
        if (!$this->cellList->contains($cellList)) {
            $this->cellList[] = $cellList;
            $cellList->setGarden($this);
        }

        return $this;
    }

    public function removeCellList(GardenCell $cellList): self
    {
        if ($this->cellList->removeElement($cellList)) {
            // set the owning side to null (unless already changed)
            if ($cellList->getGarden() === $this) {
                $cellList->setGarden(null);
            }
        }

        return $this;
    }
}
