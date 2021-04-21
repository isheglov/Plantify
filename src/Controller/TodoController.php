<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\GardenCell\AssignPlant\Service;
use App\Entity\Planning;
use App\Enumeration\PlanningStatusEnumeration;
use App\Repository\GardenRepository;
use App\Repository\PlanningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

final class TodoController extends AbstractController
{
    /** @var PlanningRepository */
    private $planningRepository;
    /** @var GardenRepository */
    private $gardenRepository;
    /** @var Security */
    private $security;
    /** @var Service */
    private $assignPlantToGardenCellService;
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param PlanningRepository $planningRepository
     */
    public function __construct(
        PlanningRepository $planningRepository,
        GardenRepository $gardenRepository,
        Security $security,
        Service $assignPlantToGardenCellService,
        EntityManagerInterface $entityManager
    ) {
        $this->planningRepository = $planningRepository;
        $this->gardenRepository = $gardenRepository;
        $this->security = $security;
        $this->assignPlantToGardenCellService = $assignPlantToGardenCellService;
        $this->entityManager = $entityManager;
    }

    public function index()
    {
        $todoList = [];

        foreach ($this->getPlannedList() as $planning) {
            $todoList[] = [
                'id' => $planning->getId(),
                'cell' => $planning->getCell()->getId(),
                'name' => $planning->getPlant()->getName(),
                'date' => $planning->getPlantAt()->format('Y F'),
            ];
        }

        return $this->render('todo/index.html.twig', [
            'todoList' => $todoList
        ]);
    }

    public function markAsDone(int $planningId)
    {
        $planning = $this->planningRepository->find($planningId);

        // assign to the cell
        $response = $this->assignPlantToGardenCellService->execute(
            $planning->getCell()->getId(),
            $planning->getPlant()->getId()
        );

        // change status to done
        $planning->setStatus(PlanningStatusEnumeration::DONE);

        $this->entityManager->persist($planning);
        $this->entityManager->flush();

        return new JsonResponse([$response]);
    }

    public function delete(int $planningId)
    {
        $planning = $this->planningRepository->find($planningId);

        $planning->setStatus(PlanningStatusEnumeration::DELETED);

        $this->entityManager->persist($planning);
        $this->entityManager->flush();

        return new JsonResponse(['ok']);
    }

    /**
     * @return Planning[]
     */
    private function getPlannedList(): array
    {
        $user = $this->security->getUser();
        $garden = $this->gardenRepository->findOneBy(['owner' => $user]);

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $gardenCellList[] = $gardenCell->getId();
        }

        $plannedList = $this->planningRepository->findBy(
                [
                    'cell' => $gardenCellList,
                    'status' => PlanningStatusEnumeration::PLANNED,
                ]
        );

        return $plannedList;
    }
}
