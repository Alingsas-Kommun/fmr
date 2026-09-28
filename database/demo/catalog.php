<?php

/**
 * Fixed political structure for the FMR Demo municipality.
 *
 * Person names are generated separately. Seat counts for parties sum to the council size.
 */

return [
    'municipality' => 'FMR Demo',
    'tagline' => 'Förtroendemannaregister för FMR Demo',
    'city' => 'Municipality',
    'zip' => '555 10',
    'street' => 'Storgatan 1',
    'email_domain' => 'municipality.se',
    'website' => 'https://municipality.se',
    'phone' => '010-555 10 00',
    'chair_short' => 'S',
    'first_vice_short' => 'C',
    'second_vice_short' => 'M',
    'parties' => [
        [
            'name' => 'Socialdemokraterna',
            'short' => 'S',
            'seats' => 13,
            'description' => 'Socialdemokratisk partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Moderaterna',
            'short' => 'M',
            'seats' => 8,
            'description' => 'Moderat partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Sverigedemokraterna',
            'short' => 'SD',
            'seats' => 6,
            'description' => 'Sverigedemokratisk partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Centerpartiet',
            'short' => 'C',
            'seats' => 5,
            'description' => 'Centerpartistisk partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Vänsterpartiet',
            'short' => 'V',
            'seats' => 3,
            'description' => 'Vänsterpartistisk partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Kristdemokraterna',
            'short' => 'KD',
            'seats' => 2,
            'description' => 'Kristdemokratisk partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Liberalerna',
            'short' => 'L',
            'seats' => 2,
            'description' => 'Liberal partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Miljöpartiet',
            'short' => 'MP',
            'seats' => 1,
            'description' => 'Miljöpartistisk partigrupp i FMR Demo.',
        ],
        [
            'name' => 'Lokalpartiet',
            'short' => 'LP',
            'seats' => 1,
            'description' => 'Lokalt parti i FMR Demo.',
        ],
    ],
    'types' => [
        'Kommunfullmäktige',
        'Nämnd',
        'Styrelse',
    ],
    'roles' => [
        ['name' => 'Ordförande', 'key' => 'chair'],
        ['name' => '1:e vice ordförande', 'key' => 'first_vice'],
        ['name' => '2:e vice ordförande', 'key' => 'second_vice'],
        ['name' => 'Ledamot', 'key' => 'member'],
        ['name' => 'Ersättare', 'key' => 'substitute'],
    ],
    'periods' => [
        // The 2026 term opens on 15 September so it is already the active period.
        // It ends on the ordinary mandate date, 14 October 2030.
        [
            'key' => '2022',
            'title' => 'Mandatperiod 2022–2026',
            'start' => '2022-10-15',
            'end' => '2026-09-14',
        ],
        [
            'key' => '2026',
            'title' => 'Mandatperiod 2026–2030',
            'start' => '2026-09-15',
            'end' => '2030-10-14',
            'current' => true,
        ],
    ],
    'boards' => [
        [
            'name' => 'Kommunfullmäktige',
            'short' => 'KF',
            'type' => 'Kommunfullmäktige',
            'seats' => 41,
            'substitutes' => 0,
            'kind' => 'council',
        ],
        [
            'name' => 'Kommunstyrelsen',
            'short' => 'KS',
            'type' => 'Styrelse',
            'seats' => 11,
            'substitutes' => 11,
            'kind' => 'board',
        ],
        [
            'name' => 'Socialnämnden',
            'short' => 'SN',
            'type' => 'Nämnd',
            'seats' => 9,
            'substitutes' => 9,
            'kind' => 'board',
        ],
        [
            'name' => 'Barn- och ungdomsnämnden',
            'short' => 'BUN',
            'type' => 'Nämnd',
            'seats' => 9,
            'substitutes' => 9,
            'kind' => 'board',
        ],
        [
            'name' => 'Vård- och omsorgsnämnden',
            'short' => 'VON',
            'type' => 'Nämnd',
            'seats' => 7,
            'substitutes' => 7,
            'kind' => 'board',
        ],
        [
            'name' => 'Miljö- och byggnämnden',
            'short' => 'MBN',
            'type' => 'Nämnd',
            'seats' => 7,
            'substitutes' => 7,
            'kind' => 'board',
        ],
        [
            'name' => 'Kultur- och fritidsnämnden',
            'short' => 'KFN',
            'type' => 'Nämnd',
            'seats' => 7,
            'substitutes' => 7,
            'kind' => 'board',
        ],
        [
            'name' => 'Valnämnden',
            'short' => 'VN',
            'type' => 'Nämnd',
            'seats' => 5,
            'substitutes' => 5,
            'kind' => 'board',
        ],
    ],
    'resignations' => [
        ['board' => 'SN', 'period' => '2022', 'end' => '2024-06-30', 'replacement_start' => '2024-07-01'],
        ['board' => 'BUN', 'period' => '2022', 'end' => '2024-06-30', 'replacement_start' => '2024-07-01'],
    ],
];
