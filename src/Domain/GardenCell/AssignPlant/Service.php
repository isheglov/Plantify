<?php

declare(strict_types=1);

namespace App\Domain\GardenCell\AssignPlant;

use App\Entity\GardenCell;
use App\Entity\History;
use App\Entity\Plant;
use App\Repository\GardenCellRepository;
use App\Repository\HistoryRepository;
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
    /** @var HistoryRepository */
    private $historyRepository;

    public function __construct(
        GardenCellRepository $gardenCellRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager,
        HistoryRepository $historyRepository
    ) {
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
        $this->gardenCellRepository = $gardenCellRepository;
        $this->historyRepository = $historyRepository;
    }

    public function execute(int $cellId, int $plantId): string
    {
        /** @var GardenCell $gardenCell */
        $gardenCell = $this->gardenCellRepository->find($cellId);

        $this->storeOldHistory($gardenCell);

        $plant = $this->setNewPlant($plantId, $gardenCell);

        $this->storeNewHistory($gardenCell, $plant);

        $this->entityManager->flush();

        return "ok";
    }

    /**
     * @param GardenCell $gardenCell
     */
    private function storeOldHistory(GardenCell $gardenCell): void
    {
        $oldPlantHistoryItem = $this->historyRepository->findOneBy(
            [
                'cell' => $gardenCell,
                'plant' => $gardenCell->getPlant(),
                'plantedTo' => null,
            ]
        );

        if ($oldPlantHistoryItem !== null) {
            $oldPlantHistoryItem
                ->setPlantedTo(new DateTime())
                ->setUpdatedAt(new DateTime());

            $this->entityManager->persist($oldPlantHistoryItem);
        }
    }

    /**
     * @param int $plantId
     * @param GardenCell $gardenCell
     * @return Plant|null
     */
    private function setNewPlant(int $plantId, GardenCell $gardenCell): ?Plant
    {
        $plant = $this->plantRepository->find($plantId);

        $gardenCell->setPlant($plant);

        $this->entityManager->persist($gardenCell);

        return $plant;
    }

    /**
     * @param GardenCell $gardenCell
     * @param Plant|null $plant
     */
    private function storeNewHistory(GardenCell $gardenCell, ?Plant $plant): void
    {
        $history = (new History())
            ->setCell($gardenCell)
            ->setPlant($plant)
            ->setPlantedFrom(new DateTime())
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($history);
    }
}
