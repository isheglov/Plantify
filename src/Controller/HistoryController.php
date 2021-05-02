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

    public function index(?int $year)
    {
        $year = $year ?? (int)(new DateTime())->format("Y");

        setlocale(LC_TIME, "ru_RU");

        $historyList = [];
        foreach ($this->getHistoryEntityList($year) as $historyItem) {
            $historyList[$historyItem->getCell()->getId()][] = [
                'name' => $historyItem->getPlant()->getName(),
                'dateFrom' => strftime("%B", $historyItem->getPlantedFrom()->getTimestamp()),
            ];
        }

        $garden = $this->getGarden();

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $gardenCellList[$gardenCell->getPositionX()][$gardenCell->getPositionY()] = [
                'plantName' => 'пусто',
            ];

            if (empty($historyList[$gardenCell->getId()])) {
                continue;
            }

            $plantName = [];
            foreach ($historyList[$gardenCell->getId()] as $cell) {
                $plantName[] = $cell['name'] . ' с ' . $cell['dateFrom'];
            }

            $gardenCellList[$gardenCell->getPositionX()][$gardenCell->getPositionY()]['plantName'] =
                implode("\n", $plantName);
        }

        $response = [
            'dimensionX' => $garden->getDimensionX(),
            'dimensionY' => $garden->getDimensionY(),
            'gardenCellList' => $gardenCellList,
            'date' => $year,
            'prev_year' => $this->prevYear($year),
            'next_year' => $this->nextYear($year),
        ];

        return $this->render('history/index.html.twig', $response);
    }

    /**
     * @return History[]
     */
    private function getHistoryEntityList(int $year): array
    {
        $garden = $this->getGarden();

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $gardenCellList[] = $gardenCell->getId();
        }

        return $this->historyRepository->findByCellAndYear($gardenCellList, $year);
    }

    /**
     * @return Garden
     */
    private function getGarden(): Garden
    {
        $user = $this->security->getUser();

        return $this->gardenRepository->findOneBy(['owner' => $user]);
    }

    /**
     * @param int $year
     * @return int
     */
    private function nextYear(int $year): int
    {
        if ((int)(new DateTime())->format("Y") <= $year) {
            return 0;
        }

        return $year + 1;
    }

    /**
     * @param int $year
     * @return int
     */
    private function prevYear(int $year): int
    {
        $prevYear = max($year - 1, 2020);

        if ($prevYear === $year) {
            return 0;
        }

        return $prevYear;
    }
}
