<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HistoryController extends AbstractController
{
    /** @var HistoryRepository */
    private $historyRepository;

    /**
     * @param HistoryRepository $historyRepository
     */
    public function __construct(HistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    public function index()
    {
        $historyList = [];

        // add filter by month ?
        foreach ($this->historyRepository->FindAll() as $historyItem) {
            $historyList[] = [
                'id' => $historyItem->getId(),
                'cell' => $historyItem->getCell()->getId(),
                'name' => $historyItem->getPlant()->getName(),
                'dateFrom' => $historyItem->getPlantedFrom() ? $historyItem->getPlantedFrom()->format('Y-m') : '',
                'dateTo' => $historyItem->getPlantedTo() ? $historyItem->getPlantedTo()->format('Y-m') : '',
            ];
        }

        return $this->render('history/index.html.twig', [
            'historyList' => $historyList
        ]);
    }
}
