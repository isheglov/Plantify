<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Garden;
use App\Entity\Planning;
use App\Entity\Planting;
use App\Enumeration\PlanningStatusEnumeration;
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
use Symfony\Component\Security\Core\Security;

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
    /** @var Security */
    private $security;

    public function __construct(
        GardenRepository $gardenRepository,
        GardenCellRepository $gardenCellRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager,
        PlanningRepository $planningRepository,
        Security $security
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
        $this->gardenCellRepository = $gardenCellRepository;
        $this->planningRepository = $planningRepository;
        $this->security = $security;
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
                'plannedPlantAt' => $plan ? $plan->getPlantAt()->format('Y-m') : '',
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

        $plant = $this->plantRepository->find($plantCellMap['plantId']);

        $plantingList = $plant->getPlantings();

        if (empty($plantingList)) {
            return new JsonResponse(['error']);
        }

        /** @var Planting $planting */
        $planting = $plantingList->first();

        $plantAt = $this->obtainPlantingDate($planting);

        $planning = (new Planning())
            ->setCell($this->gardenCellRepository->find($plantCellMap['cellId']))
            ->setPlant($plant)
            ->setPlantAt($plantAt)
            ->setStatus(PlanningStatusEnumeration::PLANNED)
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
        $user = $this->security->getUser();

        return $this->gardenRepository->findOneBy(['owner' => $user]);
    }

    /**
     * @param Planting $planting
     * @return DateTime
     */
    private function obtainPlantingDate(Planting $planting): DateTime
    {
        $monthPlanned = $this->obtainMonth($planting);

        $yearPlanned = $this->obtainPlantingYear($monthPlanned);

        return (new DateTime())->setDate($yearPlanned, $monthPlanned, 1);
    }

    /**
     * @param Planting $planting
     * @return int
     */
    private function obtainMonth(Planting $planting): int
    {
        $monthMap = [
            'январь' => 1,
            'февраль' => 2,
            'март' => 3,
            'апрель' => 4,
            'май' => 5,
            'июнь' => 6,
            'июль' => 7,
            'август' => 8,
            'сентябрь' => 9,
            'октябрь' => 10,
            'ноябрь' => 11,
            'декабрь' => 12,
        ];

        return $monthMap[mb_strtolower($planting->getPlantingMonth())];
    }

    /**
     * @param int $monthPlanned
     * @return int
     */
    private function obtainPlantingYear(int $monthPlanned): int
    {
        $monthCurrent = (int)date("m");

        if ($monthCurrent > $monthPlanned) {
            return (int)date("Y") + 1;
        }

        return (int)date("Y");
    }
}
