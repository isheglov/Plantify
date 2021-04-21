<?php

declare(strict_types=1);

namespace App\Domain\GardenCell\AssignPlant;

use App\Entity\History;
use App\Repository\GardenCellRepository;
use App\Repository\PlantRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class Service
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var PlantRepository */
    private $plantRepository;
    /** @var GardenCellRepository */
    private $gardenCellRepository;

    public function __construct(
        GardenCellRepository $gardenCellRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
        $this->gardenCellRepository = $gardenCellRepository;
    }

    public function execute(int $cellId, int $plantId): string
    {
        $gardenCell = $this->gardenCellRepository->find($cellId);

        $plant = $this->plantRepository->find($plantId);

        $gardenCell->setPlant($plant);

        $this->entityManager->persist($gardenCell);
        $this->entityManager->flush();


        $history = (new History())
            ->setCell($gardenCell)
            ->setPlant($plant)
            ->setPlantedFrom(new DateTime())
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
        ;

        $this->entityManager->persist($history);
        $this->entityManager->flush();

        return "ok";
    }
}
