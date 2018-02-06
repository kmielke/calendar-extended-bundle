<?php

/*
 * This file is part of CalendarExtendedBundle.
 *
 * Copyright (c) 2009-2018 Kester Mielke
 *
 * @license LGPL-3.0+
 */

declare(strict_types = 1);

namespace Kmielke\CalendarExtendedBundle\ContaoManager;

use Kmielke\CalendarExtendedBundle\CalendarExtendedBundle;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

/**
 * Plugin for the Contao Manager.
 *
 * @author Kester Mielke <https://github.com/kmielke>
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
                        'MenAtWork\MultiColumnWizard'
                    ]
                )
        ];
    }
}
