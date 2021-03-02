<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Garden;
use App\Entity\GardenCell;
use App\Repository\GardenRepository;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GardenController extends AbstractController
{
    /** @var GardenRepository */
    private $gardenRepository;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var PlantRepository */
    private $plantRepository;

    public function __construct(
        GardenRepository $gardenRepository,
        PlantRepository $plantRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->plantRepository = $plantRepository;
        $this->entityManager = $entityManager;
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
            ->add('rows', IntegerType::class, ['label' => 'Строк'])
            ->add('cells', IntegerType::class, ['label' => 'Столбцов'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $garden = new Garden();
            $garden
                ->setDimensionX($data['cells'])
                ->setDimensionY($data['rows'])
            ;

            $this->entityManager->persist($garden);
            $this->entityManager->flush();

            for ($i=0;$i<$data['cells'];$i++) {
                for ($j=0;$j<$data['rows'];$j++) {
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

    public function delete(Request $request): Response
    {
        foreach ($this->gardenRepository->findAll() as $garden) {
            foreach ($garden->getCellList() as $gardenCell) {
                $this->entityManager->remove($gardenCell);
            }

            $this->entityManager->remove($garden);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('user_index');
    }
}
