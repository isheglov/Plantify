<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Planting\ObtainDate\Service;
use App\Entity\Garden;
use App\Entity\GardenCell;
use App\Entity\Planning;
use App\Entity\Plant;
use App\Entity\Planting;
use App\Enumeration\PlanningStatusEnumeration;
use App\Repository\GardenCellRepository;
use App\Repository\GardenRepository;
use App\Repository\PlanningRepository;
use App\Repository\PlantRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    /** @var Service */
    private $obtainPlantingDateService;

    public function __construct(
        GardenRepository $gardenRepository,
        GardenCellRepository $gardenCellRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager,
        PlanningRepository $planningRepository,
        Security $security,
        Service $obtainPlantingDateService
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
        $this->gardenCellRepository = $gardenCellRepository;
        $this->planningRepository = $planningRepository;
        $this->security = $security;
        $this->obtainPlantingDateService = $obtainPlantingDateService;
    }

    public function index(Request $request, ?int $cell): Response
    {
        $gardenSchemeDto = $this->getGardenSchemeDto();

        $gardenSchemeDto['plantList'] = $this->plantRepository->findAll();
        $gardenSchemeDto['cell'] = $cell;

        return $this->render('planning/index.html.twig', $gardenSchemeDto);
    }

    public function create(Request $request): Response
    {
        $content = $request->getContent();

        $plantAmountMap = json_decode($content, true);

        $nextYearPlantList = [];

        foreach ($plantAmountMap['plantList'] as $plantParams) {
            $nextYearPlantList[] = $plantParams['plantId'];
//            echo $plantParams['plantAmount'];
        }

        $notUsedNextYearPlantList = array_flip($nextYearPlantList);

        $garden = $this->getGarden();

        $planning = [];
        foreach ($garden->getCellList() as $gardenCell) {
            if (null === $gardenCell->getPlant()) {
                foreach ($nextYearPlantList as $plantId) {
                    /** @var Plant $plant */
                    $plant = $this->plantRepository->find($plantId);

                    $planning = $this->addPlanningItem($gardenCell, $plant, $planning, 4);
                }

                continue;
            }

            foreach ($gardenCell->getPlant()->getFollower() as $follower) {
                if (in_array($follower->getId(), $nextYearPlantList, true)) {
                    $planning = $this->addPlanningItem($gardenCell, $follower, $planning, 4);

                    unset($notUsedNextYearPlantList[$follower->getId()]);
                }
            }
        }

        foreach ($garden->getCellList() as $gardenCell) {
            if (null === $gardenCell->getPlant()) {
                continue;
            }

            foreach ($notUsedNextYearPlantList as $plantId => $key) {
                $plant = $this->plantRepository->find($plantId);

                $planning = $this->addPlanningItem($gardenCell, $plant, $planning, 10);
            }
        }

//        result = '{"map":[{"cellId":172,"plantId":12,"plantingId":14,"plantName":"Укроп","priority":4}]}';

        $map = ['map' => $planning];

        return new JsonResponse($map);
    }

    /**
     * { "plantId":13, "plantingId":123, "cellId":190 }
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

        $planting = $this->findPlantingById($plantingList, $plantCellMap['plantingId']);

        $plantAt = $this->obtainPlantingDateService->execute($planting);

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
    private function getGarden(): Garden
    {
        $user = $this->security->getUser();

        return $this->gardenRepository->findOneBy(['owner' => $user]);
    }

    private function addPlanningItem(GardenCell $gardenCell, Plant $plant, array $planning, int $priority): array
    {
        foreach ($plant->getPlantings() as $planting) {
            $plantAt = $this->obtainPlantingDateService->execute($planting);
            $planning[] = [
                'cellId' => $gardenCell->getId(),
                'plantId' => $plant->getId(),
                'plantingId' => $planting->getId(),
                'plantName' => $plant->getName().' '.$plantAt->format('Y-m'),
                'priority' => $priority,
            ];
        }

        return $planning;
    }

    /**
     * @param $plantingList
     * @param int $plantingId
     * @return Planting
     * @throws Exception
     */
    private function findPlantingById(iterable $plantingList, int $plantingId): Planting
    {
        /** @var Planting $planting */
        foreach ($plantingList as $planting) {
            if ($planting->getId() === $plantingId) {
                return $planting;
            }
        }

        throw new Exception('Planting not found');
    }

    /**
     * @return mixed[]
     */
    private function getGardenSchemeDto(): array
    {
        $garden = $this->getGarden();

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {

            /** @var Planning $plan */
            $planningList = $this->planningRepository->findBy(
                [
                    'cell' => $gardenCell->getId(),
                    'status' => PlanningStatusEnumeration::PLANNED
                ]
            );
            $plan = empty($planningList) ? '' : end($planningList);

            $gardenCellList[$gardenCell->getPositionX()][$gardenCell->getPositionY()] = [
                'plantId' => $gardenCell->getPlant() ? $gardenCell->getPlant()->getId() : '',
                'plantName' => $gardenCell->getPlant() ? $gardenCell->getPlant()->getName() : 'пусто',
                'cellId' => $gardenCell->getId(),
                'plannedPlantName' => $plan ? $plan->getPlant()->getName() : '',
                'plannedPlantAt' => $plan ? $plan->getPlantAt()->format('Y-m') : '',
            ];
        }

        return [
            'dimensionX' => $garden->getDimensionX(),
            'dimensionY' => $garden->getDimensionY(),
            'gardenCellList' => $gardenCellList,
        ];
    }
}
