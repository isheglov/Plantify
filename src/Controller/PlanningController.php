<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Garden;
use App\Entity\Planning;
use App\Entity\Planting;
use App\Repository\GardenCellRepository;
use App\Repository\GardenRepository;
use App\Repository\PlanningRepository;
use App\Repository\PlantRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PlanningController extends AbstractController
{
    /** @var GardenRepository */
    private $gardenRepository;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var PlantRepository */
    private $plantRepository;
    /** @var GardenCellRepository */
    private $gardenCellRepository;
    /** @var PlanningRepository */
    private $planningRepository;

    public function __construct(
        GardenRepository $gardenRepository,
        GardenCellRepository $gardenCellRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager,
        PlanningRepository $planningRepository
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
        $this->gardenCellRepository = $gardenCellRepository;
        $this->planningRepository = $planningRepository;
    }

    public function index(Request $request): Response
    {
        $garden = $this->getGarden();

        if ($garden === null) {
            return $this->redirectToRoute('user_index');
        }

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {

            /** @var Planning $plan */
            $planningList = $this->planningRepository->findBy(['cell' => $gardenCell->getId()]);
            $plan = empty($planningList) ? '' : end($planningList);

            $gardenCellList[$gardenCell->getPositionX()][$gardenCell->getPositionY()] = [
                'plantId' => $gardenCell->getPlant()?$gardenCell->getPlant()->getId():'',
                'plantName' => $gardenCell->getPlant()?$gardenCell->getPlant()->getName():'пусто',
                'cellId' => $gardenCell->getId(),
//                'plannedPlantId' => 1, //potato
                'plannedPlantName' => $plan ? $plan->getPlant()->getName() : '',
            ];
        }

        return $this->render('planning/index.html.twig', [
            'dimensionX' => $garden->getDimensionX(),
            'dimensionY' => $garden->getDimensionY(),
            'gardenCellList' => $gardenCellList,
            'plantList' => $this->plantRepository->findAll(),
        ]);
    }

    public function create(Request $request): Response
    {
        $content = $request->getContent();

        $plantAmountMap = json_decode($content, true);

        $nextYearPlantList = [];

        foreach ($plantAmountMap['plantList'] as $plantAmount) {
            $nextYearPlantList[] = $plantAmount['plantId'];
//            echo $plantAmount['plantAmount'];
        }

        /** @var Garden $garden */
        $garden = $this->getGarden();

        $gardenCellList = [];
        $planning = [];
        foreach ($garden->getCellList() as $gardenCell) {
            if (null === $gardenCell->getPlant()) {
                continue;
            }

            foreach ($gardenCell->getPlant()->getFollower() as $follower) {
                if (in_array($follower->getId(), $nextYearPlantList)) {
                    $planning[] = [
                        'cellId' => $gardenCell->getId(),
                        'plantId' => $follower->getId(),
                        'plantName' => $follower->getName(),
                        'priority' => 4,
                    ];
                }
            }
        }


//        result = '{"map":[{"cellId":172,"plantId":12,"plantName":"Укроп","priority":4}]}';

        $map = ['map' => $planning];

        return new JsonResponse($map);
    }

    /**
     * { "plantId":1, "cellId":190 }
     *
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $content = $request->getContent();

        $plantCellMap = json_decode($content, true);

        // add planning line

        // cell_id
        // plant_id
        // plant_at
        // status: [planned, planted]
        // created_at
        // updated_at

        $plant = $this->plantRepository->find($plantCellMap['plantId']);

        $plantingList = $plant->getPlantings();

        if (empty($plantingList)) {
            return new JsonResponse(['error']);
        }

        /** @var Planting $planting */
        $planting = $plantingList->first();

//        $plantAt = $planting->getPlantingMonth() + next year;
        $plantAt = new DateTime();

        $planning = (new Planning())
            ->setCell($this->gardenCellRepository->find($plantCellMap['cellId']))
            ->setPlant($plant)
            ->setPlantAt($plantAt)
            ->setStatus('planned')
            ->setComment('usual planting')
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
        ;

        $this->entityManager->persist($planning);
        $this->entityManager->flush();

        return new JsonResponse(['ok']);
    }

    /**
     * @return Garden
     */
    private function getGarden(): ?Garden
    {
        $gardenList = $this->gardenRepository->findAll();

        return end($gardenList);
    }
}
