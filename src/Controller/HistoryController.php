<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\History;
use App\Repository\GardenRepository;
use App\Repository\HistoryRepository;
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

    public function index()
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

        return $this->render('history/index.html.twig', [
            'historyList' => $historyList
        ]);
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
}
