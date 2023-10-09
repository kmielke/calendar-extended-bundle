<?php

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

/**
 * Namespace.
 */

namespace Kmielke\CalendarExtendedBundle;

/**
 * Class EventExt.
 *
 * @copyright  Kester Mielke 2010-2013
 */
class EventUrls
{
    /**
     * @param $arrEvents
     * @param $arrCalendars
     * @param $intStart
     * @param $intEnd
     * @param $objModule
     *
     * @return mixed
     */
    public function modifyEventUrl($arrEvents, $arrCalendars, $intStart, $intEnd, $objModule)
    {
        if (1 === (int) $objModule->ignore_urlparameter) {
            return $arrEvents;
        }

        foreach ($arrEvents as $k1 => $days) {
            foreach ($days as $k2 => $day) {
                foreach ($day as $k3 => $event) {
                    $eventUrl = '?day='
                        .date('Ymd', $event['startTime'])
                        .'&amp;times='.$event['startTime']
                        .','.$event['endTime'];
                    $arrEvents[$k1][$k2][$k3]['href'] .= $eventUrl;
                }
            }
        }

        return $arrEvents;
    }
}
