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
 * Class ModuleEventListExt
 *
 * @copyright  Kester Mielke 2010-2013
 * @author     Kester Mielke
 * @package    Devtools
 */
class ModuleEventlist extends \EventsExt
{

    /**
     * Current date object
     * @var \Date
     */
    protected $Date;
    protected $calConf = array();

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_eventlist';


    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['eventlist'][0]) . ' ###';
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

        // Show the event reader if an item has been selected
        if ($this->cal_readerModule > 0 && (isset($_GET['events']) || (\Config::get('useAutoItem') && isset($_GET['auto_item'])))) {
            return $this->getFrontendModule($this->cal_readerModule, $this->strColumn);
        }

        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        /** @var \PageModel $objPage */
        global $objPage;
        $blnClearInput = false;

        $intYear = \Input::get('year');
        $intMonth = \Input::get('month');
        $intDay = \Input::get('day');

        // Jump to the current period
        if (!isset($_GET['year']) && !isset($_GET['month']) && !isset($_GET['day'])) {
            switch ($this->cal_format) {
                case 'cal_year':
                    $intYear = date('Y');
                    break;

                case 'cal_month':
                    $intMonth = date('Ym');
                    break;

                case 'cal_day':
                    $intDay = date('Ymd');
                    break;
            }

            $blnClearInput = true;
        }

        $blnDynamicFormat = (!$this->cal_ignoreDynamic && in_array($this->cal_format, array('cal_day', 'cal_month', 'cal_year')));

        // Create the date object
        try {
            if ($blnDynamicFormat && $intYear) {
                $this->Date = new \Date($intYear, 'Y');
                $this->cal_format = 'cal_year';
                $this->headline .= ' ' . date('Y', $this->Date->tstamp);
            } elseif ($blnDynamicFormat && $intMonth) {
                $this->Date = new \Date($intMonth, 'Ym');
                $this->cal_format = 'cal_month';
                $this->headline .= ' ' . \Date::parse('F Y', $this->Date->tstamp);
            } elseif ($blnDynamicFormat && $intDay) {
                $this->Date = new \Date($intDay, 'Ymd');
                $this->cal_format = 'cal_day';
                $this->headline .= ' ' . \Date::parse($objPage->dateFormat, $this->Date->tstamp);
            } else {
                $this->Date = new \Date();
            }
        } catch (\OutOfBoundsException $e) {
            /** @var \PageError404 $objHandler */
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($objPage->id);
        }

        list($strBegin, $strEnd, $strEmpty) = $this->getDatesFromFormat($this->Date, $this->cal_format);

        // we will overwrite $strBegin, $strEnd if cal_format_ext is set
        if ($this->cal_format_ext != '') {
            $times = explode('|', $this->cal_format_ext);

            if (count($times) == 1) {
                $strBegin = time();
                $strEnd = strtotime($times[0], $strBegin);
            } elseif (count($times) == 2) {
                $strBegin = strtotime($times[0]) ? strtotime($times[0]) : time();
                $strEnd = strtotime($times[1], $strBegin);
            }
        }

        // we will overwrite $strBegin, $strEnd if range_date is set
        $arrRange = deserialize($this->range_date);
        if (is_array($arrRange) && $arrRange[0]['date_from']) {
            $startRange = strtotime($arrRange[0]['date_from']);
            $endRange = strtotime($arrRange[0]['date_to']);

            if ($startRange && $endRange) {
                if (checkdate(date('m', $startRange), date('d', $startRange), date('Y', $startRange)) &&
                    checkdate(date('m', $endRange), date('d', $endRange), date('Y', $endRange))
                ) {
                    $strBegin = strtotime($arrRange[0]['date_from']);
                    $strEnd = strtotime($arrRange[0]['date_to']);
                }
            }
        }

        // we have to check if we have to show recurrences and pass it to the getAllEventsExt function...
        $showRecurrences = ((int)$this->showRecurrences === 1) ? false : true;

        // Get all events
        $arrAllEvents = $this->getAllEventsExt($this->cal_calendar, $strBegin, $strEnd, array($this->cal_holiday, $showRecurrences));
        $sort = ($this->cal_order == 'descending') ? 'krsort' : 'ksort';

        // Sort the days
        $sort($arrAllEvents);

        // Sort the events
        foreach (array_keys($arrAllEvents) as $key) {
            $sort($arrAllEvents[$key]);
        }

        $arrEvents = array();
        $dateBegin = date('Ymd', $strBegin);
        $dateEnd = date('Ymd', $strEnd);

        // Step 1: get the current time
        $currTime = \Date::floorToMinute();
        // Remove events outside the scope
        foreach ($arrAllEvents as $key => $days) {
            // Do not show recurrences
            if ($showRecurrences) {
                if (($key < $dateBegin) && ($key > $dateEnd)) {
                    continue;
                }
            }

            foreach ($days as $day => $events) {
                foreach ($events as $event) {
                    // Use repeatEnd if > 0 (see #8447)
                    if (($event['repeatEnd'] ?: $event['endTime']) < $strBegin || $event['startTime'] > $strEnd)
                    {
                        continue;
                    }

                    // Skip occurrences in the past but show running events (see #8497)
                    if ($event['repeatEnd'] && $event['end'] < $strBegin)
                    {
                        continue;
                    }

                    // We have to get start and end from DB again, because start is overwritten in addEvent()
                    $objEV = $this->Database->prepare("select start, stop from tl_calendar_events where id = ?")
                        ->limit(1)->execute($event['id']);
                    $eventStart = ($objEV->start) ? $objEV->start : false;
                    $eventStop = ($objEV->stop) ? $objEV->stop : false;
                    unset($objEV);

                    if ($event['show']) {
                        // Remove events outside time scope
                        if ($this->pubTimeRecurrences && ($eventStart && $eventStop)) {
                            // Step 2: get show from/until times
                            $startTimeShow = strtotime(date('dmY') . ' ' . date('Hi', $eventStart));
                            $endTimeShow = strtotime(date('dmY') . ' ' . date('Hi', $eventStop));

                            // Compare the times...
                            if ($currTime < $startTimeShow || $currTime > $endTimeShow) {
                                continue;
                            }
                        }
                    }

                    // We take the "show from" time or the "event start" time to check the display duration limit
                    $displayStart = ($event['start']) ? $event['start'] : $event['startTime'];
                    if (strlen($this->displayDuration) > 0) {
                        $displayStop = strtotime($this->displayDuration, $displayStart);
                        if ($displayStop < $currTime) {
                            continue;
                        }
                    }

                    // Hide Events that are already started
                    if ($this->hide_started && $event['startTime'] < $currTime) {
                        continue;
                    }

                    // Show Register Info
                    unset($event['reginfo']);
                    if (class_exists('leads\leads') && $event['useRegistration']) {
                        if ($event['regperson']) {
                            $values = deserialize($event['regperson']);
                            if (is_array($values)) {
                                // Anmeldungen ermittlen und anzeigen
                                $eid = (int)$event['id'];
                                $fid = (int)$event['regform'];
                                $regCount = CalendarLeadsModel::regCountByFormEvent($fid, $eid);

                                // Werte setzen
                                $values[0]['curr'] = (int)$regCount;
                                $values[0]['mini'] = (int)$values[0]['mini'];
                                $values[0]['maxi'] = (int)$values[0]['maxi'];
                                $useMaxi = ($values[0]['maxi'] > 0) ? true : false;
                                $values[0]['free'] = ($useMaxi) ? $values[0]['maxi'] - $values[0]['curr'] : 0;

                                $event['reginfo']['mini'] = $values[0]['mini'];
                                $event['reginfo']['maxi'] = $values[0]['maxi'];
                                $event['reginfo']['curr'] = $values[0]['curr'];
                                $event['reginfo']['free'] = $values[0]['free'];
                                $event['class'] = ($useMaxi && ($values[0]['free'] > 0)) ? ' regopen' : ' regclose';
                                unset($arrsql);
                            }
                            unset($values);
                        }
                    }

                    $event['firstDay'] = $GLOBALS['TL_LANG']['DAYS'][date('w', $day)];
                    $event['firstDate'] = \Date::parse($objPage->dateFormat, $day);
//                    $event['datetime'] = date('Y-m-d', $day);

                    $event['calendar_title'] = $this->calConf[$event['pid']]['calendar'];

                    if ($this->calConf[$event['pid']]['background']) {
                        $event['bgstyle'] = $this->calConf[$event['pid']]['background'];
                    }
                    if ($this->calConf[$event['pid']]['foreground']) {
                        $event['fgstyle'] = $this->calConf[$event['pid']]['foreground'];
                    }

                    // Set endtime to starttime always...
                    if ((int)$event['addTime'] === 1 && (int)$event['ignoreEndTime'] === 1) {
                        $event['time'] = \Date::parse($objPage->timeFormat, $event['startTime']);
//                        $event['date'] = \Date::parse($objPage->datimFormat, $event['startTime']) . ' - ' .   \Date::parse($objPage->dateFormat, $event['endTime']);
//                        $event['endTime'] = '';
//                        $event['time'] = '';
//                        if ((int)$event['addTime'] === 1) {
//                            $event['time'] = \Date::parse($objPage->timeFormat, $event['startTime']);
//                        }
                    }

                    // check the repeat values
                    $unit = '';
                    if ($event['recurring']) {
                        $arrRepeat = deserialize($event['repeatEach']) ? deserialize($event['repeatEach']) : null;
                        $unit = $arrRepeat['unit'];
                    }
                    if ($event['recurringExt']) {
                        $arrRepeat = deserialize($event['repeatEachExt']) ? deserialize($event['repeatEachExt']) : null;
                        $unit = $arrRepeat['unit'];
                    }

                    // get the configured weekdays if any
                    $useWeekdays = ($weekdays = deserialize($event['repeatWeekday'])) ? true : false;

                    // Set the next date
                    $nextDate = null;
                    if ($event['repeatDates']) {
                        $arrNext = deserialize($event['repeatDates']);
                        foreach ($arrNext as $k => $nextDate) {
                            if (strtotime($nextDate) > time()) {
                                // check if we have the correct weekday
                                if ($useWeekdays && $unit === 'days') {
                                    if (!in_array(date('w', $k), $weekdays)) {
                                        continue;
                                    }
                                }
                                $nextDate = \Date::parse($objPage->datimFormat, $k);
                                break;
                            }
                        }
                        $event['nextDate'] = $nextDate;
                    }

                    // Add the event to the array
                    $arrEvents[] = $event;
                }
            }
        }

        unset($arrAllEvents, $days);
        $total = count($arrEvents);
        $limit = $total;
        $offset = 0;

        // Overall limit
        if ($this->cal_limit > 0) {
            $total = min($this->cal_limit, $total);
            $limit = $total;
        }

        // Pagination
        if ($this->perPage > 0) {
            $id = 'page_e' . $this->id;
            $page = (\Input::get($id) !== null) ? \Input::get($id) : 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                /** @var \PageError404 $objHandler */
                $objHandler = new $GLOBALS['TL_PTY']['error_404']();
                $objHandler->generate($objPage->id);
            }

            $offset = ($page - 1) * $this->perPage;
            $limit = min($this->perPage + $offset, $total);

            $objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        $strMonth = '';
        $strDate = '';
        $strEvents = '';
        $dayCount = 0;
        $eventCount = 0;
        $headerCount = 0;
        $imgSize = false;

        // Override the default image size
        if ($this->imgSize != '') {
            $size = deserialize($this->imgSize);

            if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2])) {
                $imgSize = $this->imgSize;
            }
        }

        // Parse events
        for ($i = $offset; $i < $limit; $i++) {
            $event = $arrEvents[$i];
            $blnIsLastEvent = false;

            // Last event on the current day
            if (($i + 1) == $limit || !isset($arrEvents[($i + 1)]['firstDate']) || $event['firstDate'] != $arrEvents[($i + 1)]['firstDate']) {
                $blnIsLastEvent = true;
            }

            /** @var \FrontendTemplate|object $objTemplate */
            $objTemplate = new \FrontendTemplate($this->cal_template);
            $objTemplate->setData($event);

            // Month header
            if ($strMonth != $event['month']) {
                $objTemplate->newMonth = true;
                $strMonth = $event['month'];
            }

            // Day header
            if ($strDate != $event['firstDate']) {
                $headerCount = 0;
                $objTemplate->header = true;
                $objTemplate->classHeader = ((($dayCount % 2) == 0) ? ' even' : ' odd') . (($dayCount == 0) ? ' first' : '') . (($event['firstDate'] == $arrEvents[($limit - 1)]['firstDate']) ? ' last' : '');
                $strDate = $event['firstDate'];

                ++$dayCount;
            }

            // Show the teaser text of redirect events (see #6315)
            if (is_bool($event['details'])) {
                $objTemplate->hasDetails = false;
            }

            // Add the template variables
            $objTemplate->classList = $event['class'] . ((($headerCount % 2) == 0) ? ' even' : ' odd') . (($headerCount == 0) ? ' first' : '') . ($blnIsLastEvent ? ' last' : '') . ' cal_' . $event['parent'];
            $objTemplate->classUpcoming = $event['class'] . ((($eventCount % 2) == 0) ? ' even' : ' odd') . (($eventCount == 0) ? ' first' : '') . ((($offset + $eventCount + 1) >= $limit) ? ' last' : '') . ' cal_' . $event['parent'];
            $objTemplate->readMore = specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $event['title']));
            $objTemplate->more = $GLOBALS['TL_LANG']['MSC']['more'];
            $objTemplate->locationLabel = $GLOBALS['TL_LANG']['MSC']['location'];

            // Short view
            if ($this->cal_noSpan) {
                $objTemplate->day = $event['day'];
                $objTemplate->date = $event['date'];
            }
            else
            {
                $objTemplate->day = $event['firstDay'];
                $objTemplate->date = $event['firstDate'];
            }

            $objTemplate->addImage = false;

            // Add an image
            if ($event['addImage'] && $event['singleSRC'] != '') {
                $objModel = \FilesModel::findByUuid($event['singleSRC']);

                if ($objModel === null) {
                    if (!\Validator::isUuid($event['singleSRC'])) {
                        $objTemplate->text = '<p class="error">' . $GLOBALS['TL_LANG']['ERR']['version2format'] . '</p>';
                    }
                } elseif (is_file(TL_ROOT . '/' . $objModel->path)) {
                    if ($imgSize) {
                        $event['size'] = $imgSize;
                    }

                    $event['singleSRC'] = $objModel->path;
                    $this->addImageToTemplate($objTemplate, $event);
                }
            }

            $objTemplate->showRecurrences = $showRecurrences;
            $objTemplate->enclosure = array();

            // Add enclosure
            if ($event['addEnclosure']) {
                $this->addEnclosuresToTemplate($objTemplate, $event);
            }

            $strEvents .= $objTemplate->parse();

            ++$eventCount;
            ++$headerCount;
        }

        // No events found
        if ($strEvents == '') {
            $strEvents = "\n" . '<div class="empty">' . $strEmpty . '</div>' . "\n";
        }

        // See #3672
        $this->Template->headline = $this->headline;
        $this->Template->eventcount = $eventCount;
        $this->Template->events = $strEvents;

        // Clear the $_GET array (see #2445)
        if ($blnClearInput) {
            \Input::setGet('year', null);
            \Input::setGet('month', null);
            \Input::setGet('day', null);
        }
    }
}
