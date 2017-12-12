<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;


/**
 * Reads and writes events
 *
 * @author    Kester Mielke <https://github.com/kmielke>
 */
class CalendarEventsModelExt extends \CalendarEventsModel
{

    /**
     * Find events of the current period by their parent ID
     *
     * @param integer $intPid The calendar ID
     * @param integer $intStart The start date as Unix timestamp
     * @param integer $intEnd The end date as Unix timestamp
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|\CalendarEventsModelExt[]|\CalendarEventsModelExt|null A collection of models or null if there are no events
     */
    public static function findCurrentByPid($intPid, $intStart, $intEnd, array $arrOptions = array())
    {
        $t = static::$strTable;
        $intStart = intval($intStart);
        $intEnd = intval($intEnd);

        $arrColumns = array("$t.pid=? AND (($t.startTime>=$intStart AND $t.startTime<=$intEnd) OR ($t.endTime>=$intStart AND $t.endTime<=$intEnd) OR ($t.startTime<=$intStart AND $t.endTime>=$intEnd) OR (($t.recurring=1 OR $t.recurringExt=1) AND ($t.recurrences=0 OR $t.repeatEnd>=$intStart) AND $t.startTime<=$intEnd) OR ($t.repeatFixedDates is not null AND $t.repeatEnd>=$intStart))");

        if (!BE_USER_LOGGED_IN) {
            $time = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.startTime";
        }

        return static::findBy($arrColumns, $intPid, $arrOptions);
    }


    /**
     * Find upcoming events by their parent IDs
     *
     * @param array $arrIds An array of calendar IDs
     * @param integer $intLimit An optional limit
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|\CalendarEventsModelExt[]|\CalendarEventsModelExt|null A collection of models or null if there are no events
     */
    public static function findUpcomingByPids($arrIds, $intLimit = 0, array $arrOptions = array())
    {
        if (!is_array($arrIds) || empty($arrIds)) {
            return null;
        }

        $t = static::$strTable;
        $time = \Date::floorToMinute();

        // Get upcoming events using endTime instead of startTime (see #3917)
        $arrColumns = array("($t.endTime>=$time OR (($t.recurring=1 OR $t.recurringExt=1) AND ($t.recurrences=0 OR $t.repeatEnd>=$time))) AND $t.pid IN(" . implode(',', array_map('intval', $arrIds)) . ") AND ($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1");

        if ($intLimit > 0) {
            $arrOptions['limit'] = $intLimit;
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.startTime";
        }

        return static::findBy($arrColumns, null, $arrOptions);
    }


    /**
     * Find events of the current period by their parent ID
     *
     * @param integer $intPid The calendar ID
     * @param integer $intStart The start date as Unix timestamp
     * @param integer $intEnd The end date as Unix timestamp
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|\CalendarEventsModelExt[]|\CalendarEventsModelExt|null A collection of models or null if there are no events
     */
    public static function findOverlappingByPid($intPid, $intStart, $intEnd, array $arrOptions = array())
    {
        $t = static::$strTable;
        $intStart = intval($intStart);
        $intEnd = intval($intEnd);

        $arrColumns = array("$t.pid=? AND (($t.startTime>=$intStart AND $t.startTime<=$intEnd) OR ($t.endTime>=$intStart AND $t.endTime<=$intEnd) OR ($t.startTime<=$intStart AND $t.endTime>=$intEnd) OR (($t.recurring=1 OR $t.recurringExt=1) AND ($t.recurrences=0 OR $t.repeatEnd>=$intStart) AND $t.startTime<=$intEnd) OR ($t.repeatFixedDates is not null AND $t.repeatEnd>=$intStart))");

        if (!BE_USER_LOGGED_IN) {
            $time = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.startTime";
        }

        return static::findBy($arrColumns, $intPid, $arrOptions);
    }
}
