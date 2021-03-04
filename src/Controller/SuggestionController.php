<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\GardenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class SuggestionController extends AbstractController
{
    /** @var GardenRepository */
    private $gardenRepository;

    public function __construct(
        GardenRepository $gardenRepository
    ) {
        $this->gardenRepository = $gardenRepository;
    }

    public function combined(): Response
    {
        $gardenList = $this->gardenRepository->findAll();

        if(empty($gardenList)) {
            return $this->redirectToRoute('user_index');
        }

        $garden = end($gardenList);

        $combinedSuggestionList = [];

        $plantList = [];
        foreach ($garden->getCellList() as $gardenCell) {
            if (
                $gardenCell->getPlant() !== null
                && !empty($gardenCell->getPlant()->getCompanion())
            ) {
                $plantList[$gardenCell->getPlant()->getId()] = $gardenCell->getPlant();
            }
        }

        foreach ($plantList as $plant) {

            $companionList = [];
            foreach ($plant->getCompanion() as $companion) {
                $companionList[] = $companion->getName();
            }


            $combinedSuggestionList[] = [
                'plant' => $plant->getName(),
                'companionList' => $companionList,
            ];
        }

        return new JsonResponse($combinedSuggestionList);
    }
}
