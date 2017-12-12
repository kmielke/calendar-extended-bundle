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
 * Front end module "calendar".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleFullcalendar extends \EventsExt
{

    /**
     * Current date object
     * @var \Date
     */
    protected $Date;
    protected $calConf = array();
    protected $intStart;
    protected $intEnd;


    /**
     * Redirect URL
     * @var string
     */
    protected $strLink;


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_fc_fullcalendar';


    /**
     * Do not show the module if no calendar has been selected
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['fullcalendar'][0]) . ' ###';
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
                    $this->calConf[$cal]['background'] .= '#' . $cssColor;
                }
//                if (!empty($cssOpacity)) {
//                    $this->calConf[$cal]['background'] .= 'opacity:' . ($cssOpacity / 100) . ';';
//                }
            }

            if ($objBG->fg_color) {
                list($cssColor, $cssOpacity) = deserialize($objBG->fg_color);

                if (!empty($cssColor)) {
                    $this->calConf[$cal]['foreground'] .= '#' . $cssColor;
                }
//                if (!empty($cssOpacity)) {
//                    $this->calConf[$cal]['foreground'] .= 'opacity:' . ($cssOpacity / 100) . ';';
//                }
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
            $this->cal_format = 'cal_day';
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

        list($this->intStart, $this->intEnd, $strEmpty) = $this->getDatesFromFormat($this->Date, $this->cal_format);

        if (isset($_POST['type'])) {
            /**
             * if $_POST['type']) is set then we have to handle ajax calls from fullcalendar
             *
             * We check if the given $type is an existing method
             * - if yes then call the function
             * - if no just do nothing right now (for the moment)
             */
            $type = $_POST['type'];
            if (method_exists('ModuleFullcalendar', $type)) {
                $this->$type();
            }
        } else {
            $assets_path = 'system/modules/calendar_extended/assets';

            $GLOBALS['TL_CSS'][] = $assets_path . '/fullcalendar/fullcalendar.css|static';
            $GLOBALS['TL_CSS'][] = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';

            if ($objPage->hasJQuery !== '1') {
                $GLOBALS['TL_JAVASCRIPT'][] = $assets_path . '/fullcalendar/lib/jquery.min.js|static';
            }

            $GLOBALS['TL_CSS'][] = $assets_path . '/fullcalendar/lib/cupertino/jquery-ui.min.css|static';
            $GLOBALS['TL_JAVASCRIPT'][] = $assets_path . '/fullcalendar/lib/jquery-ui.min.js|static';

            $GLOBALS['TL_JAVASCRIPT'][] = $assets_path . '/fullcalendar/lib/moment.min.js|static';
            $GLOBALS['TL_JAVASCRIPT'][] = $assets_path . '/fullcalendar/fullcalendar.js|static';
            $GLOBALS['TL_JAVASCRIPT'][] = $assets_path . '/fullcalendar/gcal.js|static';
            $GLOBALS['TL_JAVASCRIPT'][] = $assets_path . '/fullcalendar/locale-all.js|static';

            /** @var \FrontendTemplate|object $objTemplate */
            $objTemplate = new \FrontendTemplate(($this->cal_ctemplate ? $this->cal_ctemplate : 'cal_fc_default'));

            // Set some fullcalendar options
            $objTemplate->url = $this->strLink;
            $objTemplate->locale = $GLOBALS['TL_LANGUAGE'];
            $objTemplate->defaultDate = date('Y-m-d\TH:i:sP', $this->Date->tstamp);
            $objTemplate->firstDay = $this->cal_startDay;
            $objTemplate->editable = ($this->fc_editable && FE_USER_LOGGED_IN) ? true : false;
            $objTemplate->businessHours = ($this->businessHours) ? true : false;

            $objTemplate->weekNumbers = $this->weekNumbers;
            $objTemplate->weekNumbersWithinDays = $this->weekNumbersWithinDays;
            $objTemplate->eventLimit = $this->eventLimit;

            $objTemplate->confirm_drop = $GLOBALS['TL_LANG']['tl_module']['confirm_drop'];
            $objTemplate->confirm_resize = $GLOBALS['TL_LANG']['tl_module']['confirm_resize'];
            $objTemplate->fetch_error = $GLOBALS['TL_LANG']['tl_module']['fetch_error'];

            // Set the formular
            // $objTemplate->event_formular = \Form::getForm(1);

            // Render the template
            $this->Template->fullcalendar = $objTemplate->parse();
        }

        // Clear the $_GET array (see #2445)
        if ($blnClearInput) {
            \Input::setGet('year', null);
            \Input::setGet('month', null);
            \Input::setGet('day', null);
        }

    }


    /**
     * Fetch all events for the given time range
     *
     * $_POST['start'] and $_POST['end'] are set by fullcalendar
     */
    protected function fetchEvents()
    {
        $intStart = (\Input::post('start')) ? strtotime(\Input::post('start')) : $this->intStart;
        $intEnd = (\Input::post('end')) ? strtotime(\Input::post('end')) : $this->intEnd;

        // Get all events
        $arrAllEvents = $this->getAllEventsExt($this->cal_calendar, $intStart, $intEnd, array($this->cal_holiday));

        // Sort the days
        $sort = ($this->cal_order == 'descending') ? 'krsort' : 'ksort';
        $sort($arrAllEvents);

        // Sort the events
        foreach (array_keys($arrAllEvents) as $key) {
            $sort($arrAllEvents[$key]);
        }

        // Step 1: get the current time
        $currTime = \Date::floorToMinute();

        // Array of events for JSON output
        $json_events = array();
        $multiday_event = array();

        // Create the JSON of all events
        foreach ($arrAllEvents as $key => $days) {

            foreach ($days as $day => $events) {

                foreach ($events as $event) {

                    // Use repeatEnd if > 0 (see #8447)
                    if (($event['repeatEnd'] ?: $event['endTime']) < $intStart || $event['startTime'] > $intEnd) {
                        continue;
                    }

                    // Check if we have to show the current event
                    if ($event['show']) {
                        // Remove events outside time scope
                        if ($this->pubTimeRecurrences && ($event['begin'] && $event['end'])) {
                            // Step 2: get show from/until times
                            $startTimeShow = strtotime(date('dmY') . ' ' . date('Hi', $event['begin']));
                            $endTimeShow = strtotime(date('dmY') . ' ' . date('Hi', $event['end']));

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

                    // Set start and end of each event to the right format for the fullcalendar
                    $event['datetime_start'] = date('Y-m-d\TH:i:s', $event['startTime']);
                    $event['datetime_end'] = date('Y-m-d\TH:i:s', $event['endTime']);
                    $allDay = ($event['addTime']) ? false : true;

                    // Set title
                    $title = html_entity_decode($event['title']);

                    // Some options
                    $editable = ($this->fc_editable && FE_USER_LOGGED_IN) ? true : false;
                    $multiday = false;
                    $recurring = false;

                    /*
                     * Editing is allowd if we have a single or multi day event. Any kind of recurring event
                     * is not allowed right now.
                     */
                    // Disable editing if event is recurring...
                    if ($event['recurring'] || $event['recurringExt'] || $event['useExceptions']) {
                        $editable = false;
                        $recurring = true;
                    }
                    $row = deserialize($event['repeatFixedDates']);
                    if ($row[0]['new_repeat'] > 0) {
                        $editable = false;
                        $recurring = true;
                    }

                    // If event is not recurring
                    if (!$recurring) {
                        // Multi day event?
                        if (\Date::parse('dmY', $event['startTime']) != \Date::parse('dmY', $event['endTime'])) {
                            $multiday = true;
                            $recurring = false;
                        }
                    }

                    // Set the icon
                    $icon = ($recurring) ? 'fa-repeat' : 'fa-calendar-o';

                    // Set the colors of the calendar
                    $bgstyle = ($this->calConf[$event['pid']]['background']) ? $this->calConf[$event['pid']]['background'] : '';
                    $fgstyle = ($this->calConf[$event['pid']]['foreground']) ? $this->calConf[$event['pid']]['foreground'] : '';

                    // Igone if this is not the first multi day entry
                    if (array_search($event['id'], $multiday_event) === false) {
                        // Add the event to array of events
                        $json_events[] = array(
                            'id' => $event['id'],
                            'title' => $title,
                            'start' => $event['datetime_start'],
                            'end' => $event['datetime_end'],
                            'description' => $event['teaser'],
                            'allDay' => $allDay,
                            'overlap' => false,
                            'url' => $event['href'],
                            'editable' => $editable,
                            'icon' => $icon,
                            'backgroundColor' => $bgstyle,
                            'textColor' => $fgstyle
                        );
                    }

                    // Remember if multi day event
                    if ($multiday && array_search($event['id'], $multiday_event) === false) {
                        $multiday_event[] = $event['id'];
                    }
                }
            }
        }

        // Free resources
        unset($event, $events, $arrAllEvents, $multiday_event);

        // Return array of events as json
        echo json_encode($json_events);
        exit;
    }


    /**
     * Get the formular and the event data
     */
    protected function getEvent()
    {
        // Get all edit_* fields from tl_form_field
        $ff = array();
        $fields = \Database::getInstance()
            ->prepare("select name, type from tl_form_field where pid = ? and name like ?")
            ->execute(1, 'edit_%');
        if ($fields->numRows > 0) {
            while ($fields->next()) {
                $ff[$fields->name] = substr($fields->name, 5);
                $ff['t_' . $fields->name] = $fields->type;
            }
        }

        // Get the event
        $id = \Input::post('event');
        $event = \CalendarEventsModelExt::findById($id);

        // Replace the edit_* value with the db value
        foreach ($ff as $k => $v) {
            $ff[$k] = strip_tags($event->$v);
        }

        // Return the form fields
        echo json_encode($ff);
        exit;
    }


    /**
     * Update date and/or time of the event
     */
    protected function updateEventTimes()
    {
        if ($event = \Input::post('event')) {
            return $this->updateEvent($event);
        }

    }


    /**
     * Update event from form data
     */
    protected function updateEventData()
    {
        if ($event = \Input::post('event')) {
            foreach ($event as $k => $v) {
                if (strpos($k, 'edit_', 0) === false) {
                    unset($event[$k]);
                } else {
                    $n = substr($k, 5);
                    $event[$n] = $v;
                    unset($event[$k]);
                }
            }

            return $this->updateEvent($event);
        }
    }


    /**
     * Update the event
     *
     * @param $event
     * @return boolean
     */
    protected function updateEvent($event)
    {
        // Get the id of the event
        $id = $event['id'];
        unset($event['id']);

        // Get allDay value
        $allDay = ($event['allDay'] === 'true') ? true : false;
        unset($event['allDay']);

        // Check if it is allowed to edit this event
        $update_event = \CalendarEventsModelExt::findById($id);
        if ($update_event->recurring || $update_event->recurringExt || $update_event->useExceptions) {
            return false;
        }
        $row = deserialize($update_event->repeatFixedDates);
        if ($row[0]['new_repeat'] > 0) {
            return false;
        }

        // Set all relevant date and time values
        $event['startDate'] = ($event['startDate']) ? $event['startDate'] : strtotime(date('d.m.Y', $event['startTime']));
        $event['repeatEnd'] = $event['startDate'];
        if ($event['endTime']) {
            $event['repeatEnd'] = $event['endTime'];
            // Set endDate only if it was set before...
            if (strlen($update_event->endDate)) {
                $event['endDate'] = ($event['endDate']) ? $event['endDate'] : strtotime(date('d.m.Y', $event['endTime']));
            }
        }

        // Check the allDay value
        if ($allDay) {
            $event['addTime'] = '';
            $event['startTime'] = '';
            $event['endTime'] = '';
        } else {
            $event['addTime'] = 1;
        }

        // Update the event
        \Database::getInstance()
            ->prepare("update tl_calendar_events %s where id=?")
            ->set($event)->execute($id);

        return true;
    }
}
