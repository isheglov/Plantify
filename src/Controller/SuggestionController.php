<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\GardenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

final class SuggestionController extends AbstractController
{
    /** @var GardenRepository */
    private $gardenRepository;

    /** @var Security */
    private $security;

    public function __construct(
        GardenRepository $gardenRepository,
        Security $security
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->security = $security;
    }

    public function combined(): Response
    {
        $user = $this->security->getUser();
        $garden = $this->gardenRepository->findOneBy(['owner' => $user]);

        if($garden === null) {
            return $this->redirectToRoute('user_index');
        }

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
