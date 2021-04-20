<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Planning;
use App\Repository\GardenRepository;
use App\Repository\PlanningRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

final class TodoController extends AbstractController
{
    /** @var PlanningRepository */
    private $planningRepository;
    /** @var GardenRepository */
    private $gardenRepository;
    /** @var Security */
    private $security;

    /**
     * @param PlanningRepository $planningRepository
     */
    public function __construct(
        PlanningRepository $planningRepository,
        GardenRepository $gardenRepository,
        Security $security)
    {
        $this->planningRepository = $planningRepository;
        $this->gardenRepository = $gardenRepository;
        $this->security = $security;
    }

    public function index()
    {
        $todoList = [];

        foreach ($this->getPlannedList() as $planning) {
            $todoList[] = [
                'id' => $planning->getId(),
                'name' => $planning->getPlant()->getName(),
                'date' => $planning->getPlantAt()->format('Y-m'),
            ];
        }

        return $this->render('todo/index.html.twig', [
            'todoList' => $todoList
        ]);
    }

    /**
     * @return Planning[]
     */
    private function getPlannedList(): array
    {
        $user = $this->security->getUser();
        $garden = $this->gardenRepository->findOneBy(['owner' => $user]);

        $plannedList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $plannedList = array_merge(
                $plannedList,
                $this->planningRepository->findBy(
                    [
                        'cell' => $gardenCell->getId(),
                        'status' => 'planned',
                    ]
                )
            );
        }

        return $plannedList;
    }
}
