<?php

declare(strict_types=1);

namespace App\Domain\Planting\Plan;

use App\Entity\Garden;

final class Service
{
    public function execute($plantAmountMap) {
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
    }
}
