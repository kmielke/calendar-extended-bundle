<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   Contao
 * @author    Kester Mielke
 * @license   LGPL
 * @copyright Kester Mielke 2010-2013
 */


/**
 * Namespace
 */
namespace Contao;

/**
 * Class EventExt
 *
 * @copyright  Kester Mielke 2010-2013
 * @author     Kester Mielke
 * @package    Devtools
 */
class EventUrls
{
    /**
     * @param $arrEvents
     * @param $arrCalendars
     * @param $intStart
     * @param $intEnd
     * @param $objModule
     * @return mixed
     */
    public function modifyEventUrl($arrEvents, $arrCalendars, $intStart, $intEnd, $objModule)
    {
        if ((int)$objModule->ignore_urlparameter === 1) {
            return $arrEvents;
        }

        foreach ($arrEvents as $k1 => $days) {
            foreach ($days as $k2 => $day) {
                foreach ($day as $k3 => $event) {
                    $eventUrl = "?day="
                        . date("Ymd", $event['startTime'])
                        . "&amp;times=" . $event['startTime']
                        . "," . $event['endTime'];
                    $arrEvents[$k1][$k2][$k3]['href'] .= $eventUrl;
                }
            }
        }

        return $arrEvents;
    }
}
