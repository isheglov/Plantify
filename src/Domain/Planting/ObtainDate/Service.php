<?php

declare(strict_types=1);

namespace App\Domain\Planting\ObtainDate;

use App\Entity\Planting;
use App\Enumeration\MonthEnumeration;
use DateTime;

final class Service
{
    public function execute(Planting $planting): DateTime
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
        return MonthEnumeration::MONTH_TO_NUMBER[mb_strtolower($planting->getPlantingMonth())];
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
