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
 * Class ModuleTimeTableExt
 *
 * @copyright  Kester Mielke 2010-2013
 * @author     Kester Mielke
 * @package    Devtools
 */
class ModuleTimeTable extends \EventsExt
{

    /**
     * Current date object
     * @var integer
     */
    protected $Date;
    protected $weekBegin;
    protected $weekEnd;
    protected $calConf = array();

    /**
     * Redirect URL
     * @var string
     */
    protected $strLink;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_calendar';

    /**
     * Do not show the module if no calendar has been selected
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['timetable'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar, true));
        $this->cal_holiday = $this->sortOutProtected(deserialize($this->cal_holiday, true));

        // Return if there are no calendars
        if (!is_array($this->cal_calendar) || empty($this->cal_calendar)) {
            return '';
        }

        // Calendar filter
        if (\Input::get('cal')) {
            // Create array of cal_id's to filter 
            $cals1 = explode(',', \Input::get('cal'));
            // Check if the cal_id's are valid for this module
            $cals2 = array_intersect($cals1, $this->cal_calendar);
            if ($cals2) {
                $this->cal_calendar = array_intersect($cals2, $this->cal_calendar);
            }
        }

        // Get the background and foreground colors of the calendars
        foreach (array_merge($this->cal_calendar, $this->cal_holiday) as $cal) {
            $objBG = $this->Database->prepare("select title, bg_color, fg_color from tl_calendar where id = ?")
                ->limit(1)->execute($cal);

            $this->calConf[$cal]['calendar'] = $objBG->title;

            if ($objBG->bg_color) {
                list($cssColor, $cssOpacity) = deserialize($objBG->bg_color);

                if (!empty($cssColor)) {
                    $this->calConf[$cal]['background'] .= 'background-color:#' . $cssColor . ';';
                }
                if (!empty($cssOpacity)) {
                    $this->calConf[$cal]['background'] .= 'opacity:' . ($cssOpacity / 100) . ';';
                }
            }

            if ($objBG->fg_color) {
                list($cssColor, $cssOpacity) = deserialize($objBG->fg_color);

                if (!empty($cssColor)) {
                    $this->calConf[$cal]['foreground'] .= 'color:#' . $cssColor . ';';
                }
                if (!empty($cssOpacity)) {
                    $this->calConf[$cal]['foreground'] .= 'opacity:' . ($cssOpacity / 100) . ';';
                }
            }
        }

        $this->strUrl = preg_replace('/\?.*$/', '', \Environment::get('request'));
        $this->strLink = $this->strUrl;

        if ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null) {
            /** @var \PageModel $objTarget */
            $this->strLink = $objTarget->getFrontendUrl();
        }

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile()
    {
        // Create the date object
        try {
            // Respond to month
            if (\Input::get('month')) {
                $this->Date = new \Date(\Input::get('month') . '01', 'Ymd');
            } // Respond to week
            elseif (\Input::get('week')) {
                $selYear = (int)substr(\Input::get('week'), 0, 4);
                $selWeek = (int)substr(\Input::get('week'), -2);
                $selDay = ($selWeek == 1) ? 4 : 1;
                $dt = new \DateTime();
                $dt->setISODate($selYear, $selWeek, $selDay);
                $this->Date = new \Date($dt->format('Ymd'), 'Ymd');
                unset($dt);
            } // Respond to day
            elseif (\Input::get('day')) {
                $this->Date = new \Date(\Input::get('day'), 'Ymd');
            } // Fallback to today
            else {
                $this->Date = new \Date();
            }
        } catch (\OutOfBoundsException $e) {
            /** @var \PageModel $objPage */
            global $objPage;

            /** @var \PageError404 $objHandler */
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($objPage->id);
        }

        // Get the Year and the week of the given date
        $intYear = (int)date('o', $this->Date->tstamp);
        $intWeek = (int)date('W', $this->Date->tstamp);

        $dt = new \DateTime();

        // Set date to the first day of the given week
        $dt->setISODate($intYear, $intWeek, 1);
        $newDate = new Date($dt->format('Ymd'), 'Ymd');
        $newYear = date('Y', $newDate->tstamp);
        $newMonth = date('m', $newDate->tstamp);
        $newDay = (int)date('d', $newDate->tstamp);
        $this->weekBegin = mktime(0, 0, 0, $newMonth, $newDay, $newYear);

        // Set date to the last day of the given week
        $dt->setISODate($intYear, $intWeek, 7);
        $newDate = new Date($dt->format('Ymd'), 'Ymd');
        $newYear = date('Y', $newDate->tstamp);
        $newMonth = date('m', $newDate->tstamp);
        $newDay = (int)date('d', $newDate->tstamp);
        $this->weekEnd = mktime(23, 59, 59, $newMonth, $newDay, $newYear);

        unset($dt);

        // Get total count of weeks of the year
        if (($weeksTotal = date('W', mktime(0, 0, 0, 12, 31, $intYear))) == 1) {
            $weeksTotal = date('W', mktime(0, 0, 0, 12, 24, $intYear));
        }

        $time = \Date::floorToMinute();

        // Find the boundaries
        $objMinMax = $this->Database->query("SELECT MIN(startTime) AS dateFrom, MAX(endTime) AS dateTo, MAX(repeatEnd) AS repeatUntil FROM tl_calendar_events WHERE pid IN(" . implode(',', array_map('intval', $this->cal_calendar)) . ")" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND published=1" : ""));
        $intLeftBoundary = date('YW', $objMinMax->dateFrom);
        $intRightBoundary = date('YW', max($objMinMax->dateTo, $objMinMax->repeatUntil));

        /** @var \FrontendTemplate|object $objTemplate */
        $objTemplate = new \FrontendTemplate(($this->cal_ctemplate ? $this->cal_ctemplate : 'cal_timetable'));

        $objTemplate->intYear = $intYear;
        $objTemplate->intWeek = $intWeek;
        $objTemplate->weekBegin = $this->weekBegin;
        $objTemplate->weekEnd = $this->weekEnd;

        $objTemplate->cal_times = $this->cal_times;
        $objTemplate->use_navigation = $this->use_navigation;
        $objTemplate->linkCurrent = $this->linkCurrent;

        // display the navigation if selected
        if ($this->use_navigation) {
            // Get the current year and the week
            if ($this->linkCurrent) {
                $currYear = date('o');
                $currWeek = (int)date('W');
                $lblCurrent = $GLOBALS['TL_LANG']['MSC']['curr_week'];
                $objTemplate->currHref = $this->strUrl . (\Config::get('disableAlias') ? '?id=' . \Input::get('id') . '&amp;' : '?') . 'week=' . $currYear . str_pad($currWeek, 2, 0, STR_PAD_LEFT);
                $objTemplate->currTitle = specialchars($lblCurrent);
                $objTemplate->currLink = $lblCurrent;
                $objTemplate->currLabel = $GLOBALS['TL_LANG']['MSC']['cal_previous'];
            }

            // Previous week
            $prevWeek = ($intWeek == 1) ? $weeksTotal : ($intWeek - 1);
            $prevYear = ($intWeek == 1) ? ($intYear - 1) : $intYear;
            $lblPrevious = $GLOBALS['TL_LANG']['MSC']['calendar_week'] . ' ' . $prevWeek . ' ' . $prevYear;
            $intPrevYm = intval($prevYear . str_pad($prevWeek, 2, 0, STR_PAD_LEFT));

//            if ($intPrevYm >= $intLeftBoundary)
//            {
            $objTemplate->prevHref = $this->strUrl . (\Config::get('disableAlias') ? '?id=' . \Input::get('id') . '&amp;' : '?') . 'week=' . $prevYear . str_pad($prevWeek, 2, 0, STR_PAD_LEFT);
            $objTemplate->prevTitle = specialchars($lblPrevious);
            $objTemplate->prevLink = $GLOBALS['TL_LANG']['MSC']['cal_previous'] . ' ' . $lblPrevious;
            $objTemplate->prevLabel = $GLOBALS['TL_LANG']['MSC']['cal_previous'];
//            }

            // Current week
            $dateInfo = \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $this->weekBegin) . ' - ' .
                \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $this->weekEnd);

            $objTemplate->current = $GLOBALS['TL_LANG']['MSC']['calendar_week'] . ' ' . $intWeek . ' ' . $intYear;

            // Next month
            // Next month
            $nextWeek = ($intWeek == $weeksTotal) ? 1 : ($intWeek + 1);
            $nextYear = ($intWeek == $weeksTotal) ? ($intYear + 1) : $intYear;
            $lblNext = $GLOBALS['TL_LANG']['MSC']['calendar_week'] . ' ' . $nextWeek . ' ' . $nextYear;
            $intNextYm = $nextYear . str_pad($nextWeek, 2, 0, STR_PAD_LEFT);

            // Only generate a link if there are events (see #4160)
//            if ($intNextYm <= $intRightBoundary)
//            {
            $objTemplate->nextHref = $this->strUrl . (\Config::get('disableAlias') ? '?id=' . \Input::get('id') . '&amp;' : '?') . 'week=' . $nextYear . str_pad($nextWeek, 2, 0, STR_PAD_LEFT);
            $objTemplate->nextTitle = specialchars($lblNext);
            $objTemplate->nextLink = $lblNext . ' ' . $GLOBALS['TL_LANG']['MSC']['cal_next'];
            $objTemplate->nextLabel = $GLOBALS['TL_LANG']['MSC']['cal_next'];
//            }
        }

        // Set week start day
        if (!$this->cal_startDay) {
            $this->cal_startDay = 0;
        }

        list($objTemplate->weekday, $objTemplate->times) = $this->compileDays();

        $this->Template->calendar = $objTemplate->parse();
    }


    /**
     * Return the week days and labels as array
     * @return array
     */
    protected function compileDays()
    {
        $arrDays = array();

        // if we start on Sunday we have to go back one day
        if ($this->cal_startDay == 0) {
            $this->weekBegin = strtotime(date("Y-m-d", $this->weekBegin) . " -1 day");
        }

        //Get all events
        $arrAllEvents = $this->getAllEventsExt($this->cal_calendar, $this->weekBegin, $this->weekEnd, array($this->cal_holiday));

        // we create the array of times
        if ($this->cal_times) {
            $arrTimes = array();
            $arrTimes['start'] = '23:00';
            $arrTimes['stop'] = '00:00';

            for ($i = 0; $i < 7; $i++) {
                $intCurrentDay = ($i + $this->cal_startDay) % 7;

                $intKey = date("Ymd", strtotime(date("Y-m-d", $this->weekBegin) . " +$i day"));
                $currDay = \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], strtotime(date("Y-m-d", $this->weekBegin) . " +$i day"));

                // Here we have to get the valid times
                if (is_array($arrAllEvents[$intKey])) {
                    // Here we have to get the valid times
                    foreach ($arrAllEvents[$intKey] as $v) {
                        foreach ($v as $vv) {
                            // set the times for the timetable
                            if (date('H:i', $vv['startTime']) < $arrTimes['start']) {
                                $arrTimes['start'] = date('H:00', $vv['startTime']);
                            }
                            if (date('H:i', $vv['endTime']) > $arrTimes['stop']) {
                                $h = date('H', $vv['endTime']);
                                $m = date('i', $vv['endTime']);
                                if ($m > 0) {
                                    $h += 1;
                                }
                                $arrTimes['stop'] = "$h:00";
                            }
                        }
                    }
                }
            }
            $arrTimes['start'] = substr($arrTimes['start'], 0, 2);
            $arrTimes['stop'] = substr($arrTimes['stop'], 0, 2);

            $timerange = deserialize($this->cal_times_range)[0];
            if ($timerange['time_from']) {
                $arrTimes['start'] = substr($timerange['time_from'], 0, 2);
            }
            if ($timerange['time_to']) {
                $arrTimes['stop'] = substr($timerange['time_to'], 0, 2);
            }

            $cellhight = ($this->cellhight) ? $this->cellhight : 60;

            $arrListTimes = array();
            $counter = 0;
            for ($i = $arrTimes['start']; $i <= $arrTimes['stop']; $i++) {
                $top = $cellhight * $counter;
                $strHour = str_pad($i, 2, '0', STR_PAD_LEFT);
                $arrListTimes[$strHour]['top'] = $top;
                $arrListTimes[$strHour]['class'] = (($counter % 2) == 0) ? 'even' : 'odd';
                $arrListTimes[$strHour]['label'] = "$i:00"; //top:".$top."px; position:relative;
                $arrListTimes[$strHour]['style'] = "height:" . $cellhight . "px;top:" . $top . "px;";
                $counter++;
            }
        }

        for ($i = 0; $i < 7; $i++) {
            $intCurrentDay = ($i + $this->cal_startDay) % 7;

            $intKey = date("Ymd", strtotime(date("Y-m-d", $this->weekBegin) . " +$i day"));
            $currDay = \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], strtotime(date("Y-m-d", $this->weekBegin) . " +$i day"));

            $class = ($intCurrentDay == 0 || $intCurrentDay == 6 || $intCurrentDay == 7) ? 'weekend' : 'weekday';
            $class .= (($intCurrentDay % 2) == 0) ? ' even' : ' odd';
            $class .= ' ' . strtolower($GLOBALS['TL_LANG']['DAYS'][$intCurrentDay]);
            $class .= ($intCurrentDay == 0) ? ' last' : '';

            if ($currDay == \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], strtotime(date("Y-m-d")))) {
                $class .= ' today';
            }

            $arrDays[$intCurrentDay]['label'] = $GLOBALS['TL_LANG']['DAYS'][$intCurrentDay];
            $arrDays[$intCurrentDay]['label_day'] = $GLOBALS['TL_LANG']['DAYS'][$intCurrentDay];
            $arrDays[$intCurrentDay]['label_date'] = $currDay;

            if ($this->showDate) {
                $arrDays[$intCurrentDay]['label'] .= '<br/>' . $currDay;
            }
            $arrDays[$intCurrentDay]['class'] = $class;

            // Get all events of a day
            $arrEvents = array();
            if (is_array($arrAllEvents[$intKey])) {
                foreach ($arrAllEvents[$intKey] as $v) {
                    foreach ($v as $vv) {
                        // set class recurring
                        if ($vv['recurring'] || $vv['recurringExt']) {
                            $vv['class'] .= ' recurring';
                        }

                        // set color from calendar
                        $vv['calendar_title'] = $this->calConf[$vv['pid']]['calendar'];

                        if ($this->calConf[$vv['pid']]['background']) {
                            $vv['bgstyle'] = $this->calConf[$vv['pid']]['background'];
                        }
                        if ($this->calConf[$vv['pid']]['foreground']) {
                            $vv['fgstyle'] = $this->calConf[$vv['pid']]['foreground'];
                        }

                        // calculate the position of the event
                        $h = date('H', $vv['startTime']);
                        $m = date('i', $vv['startTime']);
                        if (is_array($arrListTimes[$h])) {
                            // calculate the top of the event
                            $top = $arrListTimes[$h]['top'] + $m;

                            // calculate the height of the event.
                            $d1 = date_create(date('H:i', $vv['startTime']));
                            $d2 = date_create(date('H:i', $vv['endTime']));
                            $d0 = date_diff($d1, $d2);
                            $height = ($d0->format('%h') * $cellhight) + $d0->format('%i');

                            $vv['style'] .= 'position:absolute;top:' . $top . 'px;height:' . $height . 'px;';
                        }

                        $arrEvents[] = $vv;
                    }
                }
                $arrDays[$intCurrentDay]['events'] = $arrEvents;
            } else {
                $arrDays[$intCurrentDay]['events'] = $arrEvents;

                //Remove day from array if the is no event
                if ($this->hideEmptyDays) {
                    unset($arrDays[$intCurrentDay]);
                }
            }
        }

        return array($arrDays, $arrListTimes);
    }

}
