<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Garden;
use App\Entity\GardenCell;
use App\Entity\History;
use App\Repository\GardenCellRepository;
use App\Repository\GardenRepository;
use App\Repository\HistoryRepository;
use App\Repository\PlanningRepository;
use App\Repository\PlantRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

final class GardenController extends AbstractController
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
    /** @var HistoryRepository */
    private $historyRepository;

    public function __construct(
        GardenRepository $gardenRepository,
        GardenCellRepository $gardenCellRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager,
        PlanningRepository $planningRepository,
        HistoryRepository $historyRepository
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
        $this->gardenCellRepository = $gardenCellRepository;
        $this->planningRepository = $planningRepository;
        $this->historyRepository = $historyRepository;
    }

    public function index(Request $request): Response
    {
        $gardenList = $this->gardenRepository->findAll();

        if(empty($gardenList)) {
            return $this->redirectToRoute('user_index');
        }

        $garden = end($gardenList);

        $gardenCellList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            $gardenCellList[$gardenCell->getPositionX()][$gardenCell->getPositionY()] = [
                'plantId' => $gardenCell->getPlant()?$gardenCell->getPlant()->getId():'',
                'plantName' => $gardenCell->getPlant()?$gardenCell->getPlant()->getName():'пусто',
                'cellId' => $gardenCell->getId(),
            ];
        }

        return $this->render('garden/index.html.twig', [
            'dimensionX' => $garden->getDimensionX(),
            'dimensionY' => $garden->getDimensionY(),
            'gardenCellList' => $gardenCellList,
            'plantList' => $this->plantRepository->findAll(),
        ]);
    }

    public function create(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('rows', IntegerType::class, [
                'label' => 'Строк',
                'constraints' => [
                    new LessThanOrEqual(10),
                    new GreaterThanOrEqual(1),
                ]
            ])
            ->add('cells', IntegerType::class, [
                'label' => 'Столбцов',
                'constraints' => [
                    new LessThanOrEqual(10),
                    new GreaterThanOrEqual(1),
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $cells = $data['cells'];
            $rows = $data['rows'];

            if ($cells > 10) {
                throw new Exception('Should be less then 10');
            }
            if ($rows > 10) {
                throw new Exception('Should be less then 10');
            }

            $garden = new Garden();
            $garden
                ->setDimensionX($cells)
                ->setDimensionY($rows)
            ;

            $this->entityManager->persist($garden);
            $this->entityManager->flush();

            for ($i=0; $i< $cells; $i++) {
                for ($j=0; $j< $rows; $j++) {
                    $gardenCell = (new GardenCell())
                        ->setPositionX($i)
                        ->setPositionY($j)
                        ->setGarden($garden)
                    ;

                    $this->entityManager->persist($gardenCell);
                    $this->entityManager->flush();

                    $garden->addCellList($gardenCell);
                }
            }

            $this->entityManager->persist($garden);
            $this->entityManager->flush();

            return $this->redirectToRoute('garden_index');
        }

        return $this->render('garden/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function assignPlantToCell(Request $request, int $cellId, int $plantId): Response
    {
        $gardenCell = $this->gardenCellRepository->find($cellId);

        $plant = $this->plantRepository->find($plantId);

        $gardenCell->setPlant($plant);

        $this->entityManager->persist($gardenCell);
        $this->entityManager->flush();

        // history

        // set previous to removed OR separate endpoint?

        $history = (new History())
            ->setCell($gardenCell)
            ->setPlant($plant)
            ->setPlantedFrom(new DateTime())
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
        ;

        $this->entityManager->persist($history);
        $this->entityManager->flush();

        return new JsonResponse(['ok']);
    }

    public function delete(Request $request): Response
    {
        foreach ($this->gardenRepository->findAll() as $garden) {
            foreach ($garden->getCellList() as $gardenCell) {
                $planningList = $this->planningRepository->findBy(['cell' => $gardenCell->getId()]);

                foreach ($planningList as $planning) {
                    $this->entityManager->remove($planning);
                }

                $historyList = $this->historyRepository->findBy(['cell' => $gardenCell->getId()]);

                foreach ($historyList as $historyItem) {
                    $this->entityManager->remove($historyItem);
                }

                $this->entityManager->remove($gardenCell);
            }

            $this->entityManager->remove($garden);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('user_index');
    }
}
