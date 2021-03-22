<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Garden;
use App\Repository\GardenCellRepository;
use App\Repository\GardenRepository;
use App\Repository\PlantRepository;
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

    public function __construct(
        GardenRepository $gardenRepository,
        GardenCellRepository $gardenCellRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
        $this->gardenCellRepository = $gardenCellRepository;
    }

    public function index(Request $request): Response
    {
        $garden = $this->getGarden();

        if ($garden === null) {
            return $this->redirectToRoute('user_index');
        }

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $gardenCellList[$gardenCell->getPositionX()][$gardenCell->getPositionY()] = [
                'plantId' => $gardenCell->getPlant()?$gardenCell->getPlant()->getId():'',
                'plantName' => $gardenCell->getPlant()?$gardenCell->getPlant()->getName():'пусто',
                'cellId' => $gardenCell->getId(),
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

//        die();

        // get next for id
        // return
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
                    $planning[$gardenCell->getPlant()->getId()] = $follower->getId();
                }
            }
        }

        return new JsonResponse($planning);
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
