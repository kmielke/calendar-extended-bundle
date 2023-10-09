<?php

/*
 * This file is part of CalendarExtendedBundle.
 *
 * Copyright (c) 2009-2018 Kester Mielke
 *
 * @license LGPL-3.0+
 */

declare(strict_types=1);

/*
 * This file is part of cgoit\calendar-extended-bundle.
 *
 * (c) Kester Mielke
 *
 * (c) Carsten Götzinger
 *
 * @license LGPL-3.0-or-later
 */

namespace Kmielke\CalendarExtendedBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Kmielke\CalendarExtendedBundle\CalendarExtendedBundle;

/**
 * Plugin for the Contao Manager.
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(CalendarExtendedBundle::class)
                ->setLoadAfter(
                    [
                        'Contao\CoreBundle\ContaoCoreBundle',
                        'Contao\CalendarBundle\ContaoCalendarBundle',
                        'MenAtWork\MultiColumnWizard',
                    ]
                ),
        ];
    }
}
