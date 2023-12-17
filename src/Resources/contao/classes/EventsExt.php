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
namespace Kmielke\CalendarExtendedBundle;

use Contao\Calendar;
use Contao\CalendarModel;
use Contao\Date;
use Contao\Events;

use Kmielke\CalendarExtendedBundle\CalendarEventsModelExt;

/**
 * Class EventExt
 *
 * @copyright  Kester Mielke 2010-2013
 * @author     Kester Mielke
 * @package    Devtools
 */
class EventsExt extends Events
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = '';


    /**
     * Generate the module
     */
    protected function compile()
    {
        parent::compile;
    }


    /**
     * Get all events of a certain period
     *
     * @param array $arrCalendars
     * @param int $intStart
     * @param int $intEnd
     * @param boolean $blnFeatured
     * @return array
     * @throws \Exception
     */
    protected function getAllEvents($arrCalendars, $intStart, $intEnd, $blnFeatured = null)
    {
        return $this->getAllEventsExt($arrCalendars, $intStart, $intEnd, array(null, true), $blnFeatured);
    }


    /**
     * Get all events of a certain period
     *
     * @param $arrCalendars
     * @param $intStart
     * @param $intEnd
     * @param null $arrParam
     * @param boolean $blnFeatured
     * @return array
     * @throws \Exception
     */
    protected function getAllEventsExt($arrCalendars, $intStart, $intEnd, $arrParam = null, $blnFeatured = null)
    {
        # set default values...
        $arrHolidays = null;
        $showRecurrences = true;

        if (!is_array($arrCalendars)) {
            return array();
        }

        $this->arrEvents = array();

        if ($arrParam !== null) {
            $arrHolidays = $arrParam[0];
            if (count($arrParam) > 1) {
                $showRecurrences = $arrParam[1];
            }
        }

        // Used to collect exception list data for events
        $arrEventSkipInfo = array();

        foreach ($arrCalendars as $id) {
            $strUrl = $this->strUrl;
            $objCalendar = CalendarModel::findByPk($id);

            // Get the current "jumpTo" page
            if ($objCalendar !== null && $objCalendar->jumpTo && ($objTarget = $objCalendar->getRelated('jumpTo')) !== null) {
                /** @var \PageModel $objTarget */
                $strUrl = $objTarget->getFrontendUrl((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/%s' : '/events/%s');
            }

            // Get the events of the current period
            $objEvents = CalendarEventsModelExt::findCurrentByPid($id, $intStart, $intEnd, array('showFeatured' => $blnFeatured));

            if ($objEvents === null) {
                continue;
            }

            while ($objEvents->next()) {
                $eventRecurrences = (int)$objEvents->recurrences + 1;

                $initStartTime = $objEvents->startTime;
                $initEndTime = $objEvents->endTime;

                $objEvents->pos_idx = 1;
                $objEvents->pos_cnt = 1;

                if ($objEvents->recurring || $objEvents->recurringExt) {
                    if ($objEvents->recurrences == 0) {
                        $objEvents->pos_cnt = 0;
                    } else {
                        $objEvents->pos_cnt = (int)$eventRecurrences;
                    }
                }

                // get the event filter data
                $filter = [];
                if ($this->filter_fields) {
                    $filter_fields = deserialize($this->filter_fields);
                    foreach ($filter_fields as $field) {
                        $filter[$field] = $objEvents->$field;
                    }
                    // filter_data can be used in the template
                    $objEvents->filter_data = json_encode($filter, JSON_FORCE_OBJECT);
                }

                // Count irregular recurrences
                $arrayFixedDates = deserialize($objEvents->repeatFixedDates) ? deserialize($objEvents->repeatFixedDates) : null;
                if (!is_null($arrayFixedDates)) {
                    foreach ($arrayFixedDates as $fixedDate) {
                        if ($fixedDate['new_repeat']) {
                            $objEvents->pos_cnt++;
                        }
                    }
                }

                // Check if we have to store the event if it's on weekend
                $weekday = (int)date('w', $objEvents->startTime);
                $store = true;
                if ($objEvents->hideOnWeekend) {
                    if ($weekday === 0 || $weekday === 6) {
                        $store = false;
                    }
                }

                // check the repeat values
                if ($objEvents->recurring) {
                    $arrRepeat = deserialize($objEvents->repeatEach) ? deserialize($objEvents->repeatEach) : null;
                }
                if ($objEvents->recurringExt) {
                    $arrRepeat = deserialize($objEvents->repeatEachExt) ? deserialize($objEvents->repeatEachExt) : null;
                }

                // we need a counter for the recurrences if noSpan is set
                $cntRecurrences = 0;
                $dateBegin = date('Ymd', $intStart);
                $dateEnd = date('Ymd', $intEnd);
                $dateNextStart = date('Ymd', $objEvents->startTime);
                $dateNextEnd = date('Ymd', $objEvents->endTime);

                // store the entry if everything is fine...
                if ($store === true) {
                    $eventEnd = $objEvents->endTime;

                    $this->addEvent($objEvents, $objEvents->startTime, $eventEnd, $strUrl, $intStart, $intEnd, $id);

                    // increase $cntRecurrences if event is in scope
                    if ($dateNextStart >= $dateBegin && $dateNextEnd <= $dateEnd) {
                        $cntRecurrences++;
                    }
                }

                // keep the original values
                $orgDateStart = new Date($objEvents->startTime);
                $orgDateEnd = new Date($objEvents->endTime);
                $orgDateSpan = Calendar::calculateSpan($objEvents->startTime, $objEvents->endTime);

                // keep the css class of the event
                $masterCSSClass = $objEvents->cssClass;

                /*
                 * Recurring events and Ext. Recurring events
                 *
                 * Here we manage the recurrences. We take the repeat option and set the new values
                 * if showRecurrences is false we do not need to go thru all recurring events...
                 */
                if ((($objEvents->recurring && $objEvents->repeatEach) || ($objEvents->recurringExt && $objEvents->repeatEachExt)) && $showRecurrences) {
                    if (is_null($arrRepeat)) {
                        continue;
                    }

                    // list of months we need
                    $arrMonth = array(1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'jun',
                        7 => 'july', 8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december',
                    );

                    $count = 0;

                    // start and end time of the event
                    $eventStartTime = Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $objEvents->startTime);
                    $eventEndTime = Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $objEvents->endTime);

                    // now we have to take care about the exception dates to skip
                    if ($objEvents->useExceptions) {
                        $arrEventSkipInfo[$objEvents->id] = deserialize($objEvents->exceptionList);
                    }

                    // get the configured weekdays if any
                    $useWeekdays = ($weekdays = deserialize($objEvents->repeatWeekday)) ? true : false;

                    // time of the next event
                    $nextTime = $objEvents->endTime;
                    while ($nextTime < $intEnd) {
                        $objEvents->pos_idx++;
                        if ($objEvents->recurrences == 0) {
                            $objEvents->pos_cnt = 0;
                        } else {
                            $objEvents->pos_cnt = (int)$eventRecurrences;
                        }

                        if ($objEvents->recurrences > 0 && $count++ >= $objEvents->recurrences) {
                            break;
                        }

                        $arg = $arrRepeat['value'] ?? null;
                        $unit = $arrRepeat['unit'] ?? null;

                        $addmonth = true;
                        if ($objEvents->recurring) {
                            // this is the contao default
                            $strtotime = '+ ' . $arg . ' ' . $unit;
                            $objEvents->startTime = strtotime($strtotime, $objEvents->startTime);
                            $objEvents->endTime = strtotime($strtotime, $objEvents->endTime);
                        } else {
                            // extended version.
                            $intyear = (int)date('Y', $objEvents->startTime);
                            $intmonth = (int)date('n', $objEvents->startTime) + 1;

                            $year = ($intmonth == 13) ? ($intyear + 1) : $intyear;
                            $month = ($intmonth == 13) ? 1 : $intmonth;

                            $strtotime = $arg . ' ' . $unit . ' of ' . $arrMonth[$month] . ' ' . $year;
                            $startTime = strtotime($strtotime . ' ' . $eventStartTime, $objEvents->startTime);
                            $endTime = strtotime($strtotime . ' ' . $eventEndTime, $objEvents->endTime);

                            $chkmonth = (int)date('n', $startTime);
                            if ($chkmonth !== $month) {
                                $addmonth = false;
                                $strtotime = 'first day of ' . $arrMonth[$month] . ' ' . $year;
                                $objEvents->startTime = strtotime($strtotime . ' ' . $eventStartTime, $startTime);
                                $objEvents->endTime = strtotime($strtotime . ' ' . $eventEndTime, $endTime);
                            } else {
                                $objEvents->startTime = $startTime;
                                $objEvents->endTime = $endTime;
                            }
                        }
                        $nextTime = $objEvents->endTime;

                        // check if we have the correct weekday
                        if ($useWeekdays && $unit === 'days') {
                            if (!in_array(date('w', $nextTime), $weekdays)) {
                                continue;
                            }
                        }

                        $oldDate = array();

                        // check if there is any exception
                        if (isset($arrEventSkipInfo[$objEvents->id]) && is_array($arrEventSkipInfo[$objEvents->id])) {
                            // modify the css class of the exceptions
                            $objEvents->cssClass = $masterCSSClass;
                            unset($objEvents->moveReason);

                            // date to search for
                            $findDate = $objEvents->startTime;
                            //  $s = strtotime(date("d.m.Y", $objEvents->startTime));
                            // $searchDate = mktime(0, 0, 0, date('m', $s), date('d', $s), date('Y', $s));

                            // store old date values for later reset
                            $oldDate = array();

                            if (isset($arrEventSkipInfo[$objEvents->id][$findDate]) && is_array($arrEventSkipInfo[$objEvents->id][$findDate])) {
                                // $r = $searchDate;
                                $r = $findDate;
                                $action = $arrEventSkipInfo[$objEvents->id][$r]['action'];
                                $cssClass = $arrEventSkipInfo[$objEvents->id][$r]['cssclass'];
                                $objEvents->cssClass .= ($cssClass) ? $cssClass . ' ' : '';

                                if ($action == "hide") {
                                    //continue the while since we don't want to show the event
                                    continue;
                                } else if ($action == "move") {
                                    //just add the css class to the event
                                    $objEvents->cssClass .= "moved";

                                    // keep old date. we have to reset it later for the next recurrence
                                    $oldDate['startTime'] = $objEvents->startTime;
                                    $oldDate['endTime'] = $objEvents->endTime;

                                    // also keep the old values in the row
                                    $objEvents->oldDate = Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $objEvents->startTime);

                                    // value to add to the old date
                                    $newDate = $arrEventSkipInfo[$objEvents->id][$r]['new_exception'];

                                    // store the reason for the move
                                    $objEvents->moveReason = $arrEventSkipInfo[$objEvents->id][$r]['reason'];

                                    // check if we have to change the time of the event
                                    if ($arrEventSkipInfo[$objEvents->id][$r]['new_start']) {
                                        $objEvents->oldStartTime = Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $objEvents->startTime);
                                        $objEvents->oldEndTime = Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $objEvents->endTime);

                                        // get the date of the event and add the new time to the new date
                                        $newStart = Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $objEvents->startTime)
                                            . ' ' . $arrEventSkipInfo[$objEvents->id][$r]['new_start'];
                                        $newEnd = Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $objEvents->endTime)
                                            . ' ' . $arrEventSkipInfo[$objEvents->id][$r]['new_end'];

                                        //set the new values
                                        $objEvents->startTime = strtotime($newDate, strtotime($newStart));
                                        $objEvents->endTime = strtotime($newDate, strtotime($newEnd));
                                    } else {
                                        $objEvents->startTime = strtotime($newDate, $objEvents->startTime);
                                        $objEvents->endTime = strtotime($newDate, $objEvents->endTime);
                                    }
                                }
                            }
                        }

                        // Skip events outside the scope
                        if ($objEvents->endTime < $intStart || $objEvents->startTime > $intEnd) {
                            // in case of a move we have to reset the original date
                            if (!empty($oldDate)) {
                                $objEvents->startTime = $oldDate['startTime'];
                                $objEvents->endTime = $oldDate['endTime'];
                            }
                            // reset this values...
                            $objEvents->moveReason = null;
                            $objEvents->oldDate = null;
                            $objEvents->oldStartTime = null;
                            $objEvents->oldEndTime = null;
                            continue;
                        }

                        // used for showOnlyNext
                        $dateNextStart = date('Ymd', $objEvents->startTime);
                        $dateNextEnd = date('Ymd', $objEvents->endTime);

                        // stop if we have on event and showOnlyNext is true
                        if ($this->showOnlyNext && $cntRecurrences > 0) {
                            break;
                        }

                        $objEvents->isRecurrence = true;

                        $weekday = date('w', $objEvents->startTime);
                        $store = true;
                        if ($objEvents->hideOnWeekend) {
                            if ($weekday == 0 || $weekday == 6) {
                                $store = false;
                            }
                        }
                        if ($store === true && $addmonth === true) {
                            $this->addEvent($objEvents, $objEvents->startTime, $objEvents->endTime, $strUrl, $intStart, $intEnd, $id);
                        }

                        // reset this values...
                        $objEvents->moveReason = null;
                        $objEvents->oldDate = null;
                        $objEvents->oldStartTime = null;
                        $objEvents->oldEndTime = null;

                        // in case of a move we have to reset the original date
                        if (!empty($oldDate)) {
                            $objEvents->startTime = $oldDate['startTime'];
                            $objEvents->endTime = $oldDate['endTime'];
                        }

                        // increase $cntRecurrences if event is in scope
                        if ($dateNextStart >= $dateBegin && $dateNextEnd <= $dateEnd) {
                            $cntRecurrences++;
                        }
                    }
                    unset($objEvents->moveReason);
                } // end if recurring...

                /*
                 * next we handle the irregular recurrences
                 *
                 * this is a complete different case
                 */
                if (!is_null($arrayFixedDates) && $showRecurrences) {
                    foreach ($arrayFixedDates as $fixedDate) {
                        if ($fixedDate['new_repeat']) {
                            // check if we have to stop because of showOnlyNext
                            if ($this->showOnlyNext && $cntRecurrences > 0) {
                                break;
                            }

                            // new start time
                            $strNewDate = $fixedDate['new_repeat'];
                            $strNewTime = (strlen($fixedDate['new_start']) ? date('H:i', $fixedDate['new_start']) : $orgDateStart->time);
                            $newDateStart = new Date(strtotime(date("d.m.Y", $strNewDate) . ' ' . $strNewTime), \Config::get('datimFormat'));
                            $objEvents->startTime = $newDateStart->timestamp;
                            $dateNextStart = date('Ymd', $objEvents->startTime);

                            // new end time
                            $strNewTime = (strlen($fixedDate['new_end']) ? date('H:i', $fixedDate['new_end']) : $orgDateEnd->time);
                            $newDateEnd = new Date(strtotime(date("d.m.Y", $strNewDate) . ' ' . $strNewTime), \Config::get('datimFormat'));

                            // use the multi-day span of the event
                            if ($orgDateSpan > 0) {
                                $newDateEnd = new Date(strtotime('+' . $orgDateSpan . ' days', $newDateEnd->timestamp), Date::getNumericDatimFormat());
                            }

                            $objEvents->endTime = $newDateEnd->timestamp;
                            $dateNextEnd = date('Ymd', $objEvents->endTime);

                            // set a reason if given...
                            $objEvents->moveReason = $fixedDate['reason'] ? $fixedDate['reason'] : null;

                            // position of the event
                            $objEvents->pos_idx++;

                            $this->addEvent($objEvents, $objEvents->startTime, $objEvents->endTime, $strUrl, $intStart, $intEnd, $id);

                            // restore the original values
                            $objEvents->startTime = $orgDateStart->timestamp;
                            $objEvents->endTime = $orgDateEnd->timestamp;

                            // increase $cntRecurrences if event is in scope
                            if ($dateNextStart >= $dateBegin && $dateNextEnd <= $dateEnd) {
                                $cntRecurrences++;
                            }
                        }
                    }
                    unset($objEvents->moveReason);
                }

                // reset times
                $objEvents->startTime = $initStartTime;
                $objEvents->endTime = $initEndTime;
            }
        }

        if ($arrHolidays !== null) {
            // run thru all holiday calendars
            foreach ($arrHolidays as $id) {
                $objAE = $this->Database->prepare("SELECT allowEvents FROM tl_calendar WHERE id = ?")
                    ->limit(1)->execute($id);
                $allowEvents = ($objAE->allowEvents === 1) ? true : false;

                $strUrl = $this->strUrl;
                $objCalendar = \CalendarModel::findByPk($id);

                // Get the current "jumpTo" page
                if ($objCalendar !== null && $objCalendar->jumpTo && ($objTarget = $objCalendar->getRelated('jumpTo')) !== null) {
                    $strUrl = $this->generateFrontendUrl($objTarget->row(), ($GLOBALS['TL_CONFIG']['useAutoItem'] ? '/%s' : '/events/%s'));
                }

                // Get the events of the current period
                $objEvents = CalendarEventsModelExt::findCurrentByPid($id, $intStart, $intEnd);

                if ($objEvents === null) {
                    continue;
                }

                while ($objEvents->next()) {
                    // at last we add the free multi-day / holiday or what ever kind of event
                    if (!$this->show_holiday) {
                        $this->addEvent($objEvents, $objEvents->startTime, $objEvents->endTime, $strUrl, $intStart, $intEnd, $id);
                    }

                    /**
                     * Multi-day event
                     * first we have to find all free days
                     */
                    $span = Calendar::calculateSpan($objEvents->startTime, $objEvents->endTime);

                    // unset the first day of the multi-day event
                    $intDate = $objEvents->startTime;
                    $key = date('Ymd', $intDate);
                    // check all events if the calendar allows events on free days
                    if ($this->arrEvents[$key]) {
                        foreach ($this->arrEvents[$key] as $k1 => $events) {
                            foreach ($events as $k2 => $event) {
                                // do not remove events from any holiday calendar
                                $isHolidayEvent = array_search($event['pid'], $arrHolidays);

                                // unset the event if showOnFreeDay is not set
                                if ($allowEvents === false) {
                                    if ($isHolidayEvent === false) {
                                        unset($this->arrEvents[$key][$k1][$k2]);
                                    }
                                } else {
                                    if ($isHolidayEvent === false && !$event['showOnFreeDay'] === 1) {
                                        unset($this->arrEvents[$key][$k1][$k2]);
                                    }
                                }
                            }
                        }
                    }

                    // unset all the other days of the multi-day event
                    for ($i = 1; $i <= $span && $intDate <= $intEnd; $i++) {
                        $intDate = strtotime('+ 1 day', $intDate);
                        $key = date('Ymd', $intDate);
                        // check all events if the calendar allows events on free days
                        if ($this->arrEvents[$key]) {
                            foreach ($this->arrEvents[$key] as $k1 => $events) {
                                foreach ($events as $k2 => $event) {
                                    // do not remove events from any holiday calendar
                                    $isHolidayEvent = array_search($event['pid'], $arrHolidays);

                                    // unset the event if showOnFreeDay is not set
                                    if ($allowEvents === false) {
                                        if ($isHolidayEvent === false) {
                                            unset($this->arrEvents[$key][$k1][$k2]);
                                        }
                                    } else {
                                        if ($isHolidayEvent === false && !$event['showOnFreeDay'] == 1) {
                                            unset($this->arrEvents[$key][$k1][$k2]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Sort the array
        foreach (array_keys($this->arrEvents) as $key) {
            ksort($this->arrEvents[$key]);
        }

        // HOOK: modify the result set
        if (isset($GLOBALS['TL_HOOKS']['getAllEvents']) && is_array($GLOBALS['TL_HOOKS']['getAllEvents'])) {
            foreach ($GLOBALS['TL_HOOKS']['getAllEvents'] as $callback) {
                $this->import($callback[0]);
                $this->arrEvents = $this->{$callback[0]}->{$callback[1]}($this->arrEvents, $arrCalendars, $intStart, $intEnd, $this);
            }
        }

        return $this->arrEvents;
    }
}
