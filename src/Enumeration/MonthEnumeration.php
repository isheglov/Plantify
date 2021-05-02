<?php

declare(strict_types=1);

namespace App\Enumeration;

final class MonthEnumeration
{
    public const MONTH_TO_NUMBER = [
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

    public const NUMBER_TO_MONTH = [
        1 => 'январь',
        2 => 'февраль',
        3 =>'март',
        4 => 'апрель',
        5 => 'май',
        6 => 'июнь',
        7 => 'июль',
        8 => 'август',
        9 => 'сентябрь',
        10 => 'октябрь',
        11 => 'ноябрь',
        12 => 'декабрь',
    ];

    public const NUMBER_TO_ROD_PADEJ = [
        1 => 'января',
        2 => 'февраля',
        3 =>'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря',
    ];
}