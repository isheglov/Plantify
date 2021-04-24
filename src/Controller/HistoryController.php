<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Garden;
use App\Entity\History;
use App\Repository\GardenRepository;
use App\Repository\HistoryRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

final class HistoryController extends AbstractController
{
    /** @var HistoryRepository */
    private $historyRepository;
    /** @var GardenRepository */
    private $gardenRepository;
    /** @var Security */
    private $security;

    public function __construct(
        HistoryRepository $historyRepository,
        GardenRepository $gardenRepository,
        Security $security
    ) {
        $this->historyRepository = $historyRepository;
        $this->gardenRepository = $gardenRepository;
        $this->security = $security;
    }

    public function index(?int $year, ?int $month)
    {
        $historyList = [];

        foreach ($this->getHistoryEntityList() as $historyItem) {
            $historyList[] = [
                'id' => $historyItem->getId(),
                'cell' => $historyItem->getCell()->getId(),
                'name' => $historyItem->getPlant()->getName(),
                'dateFrom' => $historyItem->getPlantedFrom() ? $historyItem->getPlantedFrom()->format('Y F') : '',
                'dateTo' => $historyItem->getPlantedTo() ? $historyItem->getPlantedTo()->format('Y F') : '',
            ];
        }

        $gardenScheme = $this->getGardenSchemeDto();

        $arr = [
            'historyList' => $historyList,
            'date' => (new DateTime)->format("Y m")
        ];

        $response = array_merge($gardenScheme, $arr);

        return $this->render('history/index.html.twig', $response);
    }

    /**
     * @return History[]
     */
    private function getHistoryEntityList(): array
    {
        $user = $this->security->getUser();
        $garden = $this->gardenRepository->findOneBy(['owner' => $user]);

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $gardenCellList[] = $gardenCell->getId();
        }

        return $this->historyRepository->findBy(['cell' => $gardenCellList]);
    }

    /**
     * @return mixed[]
     */
    private function getGardenSchemeDto(): array
    {
        $garden = $this->getGarden();

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $gardenCellList[$gardenCell->getPositionX()][$gardenCell->getPositionY()] = [
                'plantId' => $gardenCell->getPlant() ? $gardenCell->getPlant()->getId() : '',
                'plantName' => $gardenCell->getPlant() ? $gardenCell->getPlant()->getName() : 'пусто',
                'cellId' => $gardenCell->getId(),
            ];
        }

        return [
            'dimensionX' => $garden->getDimensionX(),
            'dimensionY' => $garden->getDimensionY(),
            'gardenCellList' => $gardenCellList,
        ];
    }

    /**
     * @return Garden
     */
    private function getGarden(): Garden
    {
        $user = $this->security->getUser();

        return $this->gardenRepository->findOneBy(['owner' => $user]);
    }
}
